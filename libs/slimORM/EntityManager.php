<?php
/**
 * User: Martin Chudoba
 * Date: 9.3.14
 * Time: 13:09
 */

namespace slimORM;

use Nette\Caching\Cache;
use Nette\Database\Context;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\Utils\FileSystem;
use Nette\Utils\LimitedScope;
use slimORM\Exceptions\RepositoryException;
use slimORM\Reflexion\EntityReflexion;

final class EntityManager
{

	/** string PREFIX */
	const PREFIX = "__slimORM__";

	/** @var array */
	private $repositories;

	/** @var \Nette\Database\Context */
	private $connection;

	/** @var array */
	private $entities;

	/** @var \Nette\Caching\Cache */
	private $cache;

	/** Constructor
	 * @param Context $connection
	 * @param Cache $cache
	 */
	public function __construct(Context $connection, Cache $cache)
	{
		$this->repositories = array();
		$this->entities = array();
		$this->connection = $connection;
		$this->cache = $cache;
	}

	/**
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 * @return \slimORM\BaseRepository
	 */
	public function getRepository($className)
	{
		$genClassName = EntityManager::PREFIX . str_replace("\\", "", $className) . "Repository";
		if (array_key_exists($genClassName, $this->repositories)) {
			return $this->repositories[$genClassName];
		} else {
			if (class_exists($className)) {
				$this->generateRepository($className);
				$this->generateEntity($className);
				return $this->repositories[$genClassName];
			} else {
				throw new RepositoryException("Class \"" . $className . "\" is not exists.");
			}
		}
	}

	/**
	 * @param string $className
	 * @return array
	 */
	final private function getColumns($className)
	{
		return EntityReflexion::getColumns($className);
	}

	/**
	 * @param string $className
	 * @return array
	 */
	final private function getReferences($className)
	{
		return EntityReflexion::getReferences($className);
	}

	/**
	 * Generate repository class
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 */
	private function generateRepository($className)
	{
		$table = EntityReflexion::getTable($className);
		if ($table === NULL) {
			throw new RepositoryException("Entity \"" . $className . " has no annotation \"table\"");
		} else {
			$genClassName = EntityManager::PREFIX . str_replace("\\", "", $className) . "Repository";
			if (!class_exists($genClassName)) {
				$class = $this->cache->load($genClassName);
				if ($class) {
					$repository = $class;
				} else {
					$repository = new ClassType($genClassName);
					$repository->addExtend("\slimORM\BaseRepository");
					$repository->setFinal(TRUE);
					$repository->addDocument($genClassName);
					$repository->addProperty("connection")
						->setVisibility("protected")
						->setDocuments(array("@var \Nette\Database\Context"));
					$parameter = new Parameter();
					$parameter->setName("connection");
					$parameter->setTypeHint("\Nette\Database\Context");
					$entity = EntityManager::PREFIX . str_replace("\\", "", $className) . "Entity";
					$repository->addMethod("__construct")
						->setParameters(array($parameter))
						->setBody("\$this->connection = \$connection;\nparent::__construct(\$connection, \"$table\", \"$entity\");");
					$parameter = new Parameter();
					$parameter->setName("key");
					$repository->addMethod("get")
						->setDocuments(array("Find item by primary key", "@param int \$key", "@return $entity|null"))
						->setParameters(array($parameter))
						->setBody("return parent::get(\$key);");

					//$file = new FileSystem();
					//$file->write(WWW_DIR . '/temp/' . $genClassName . ".php", "<?php\n" . $repository);
					$this->cache->save($genClassName, $repository);
				}
				//LimitedScope::load(WWW_DIR . '/temp/' . $genClassName . ".php", TRUE);
				LimitedScope::evaluate("<?php " . $repository);
				$this->repositories[$genClassName] = new $genClassName($this->connection);
			} else if (!isset($this->repositories[$genClassName])) {
				$this->repositories[$genClassName] = new $genClassName($this->connection);
			}
		}
	}

	/**
	 * Generate entity class
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 */
	private function generateEntity($className)
	{
		$genClassName = EntityManager::PREFIX . str_replace("\\", "", $className) . "Entity";
		$table = EntityReflexion::getTable($className);
		if ($table === NULL) {
			throw new RepositoryException("Entity \"" . $className . " has no annotation \"table\"");
		} else {
			if (in_array($genClassName, $this->entities) || class_exists($genClassName)) {
				return;
			} else {
				$this->entities[$genClassName] = $genClassName;
				$class = $this->cache->load($genClassName);
				if ($class) {
					$repository = $class;
					$references = $this->getReferences($className);
					$this->generateReferences($references);
				} else {
					$repository = new ClassType($genClassName);
					$repository->addExtend($className);
					$repository->setFinal(TRUE);
					$repository->addDocument($genClassName);
					$repository->addDocument("@table " . $table);

					$columns = $this->getColumns($className);
					$this->generateGetters($columns, $repository);

					$references = $this->getReferences($className);
					$this->generateReferences($references, $repository);

					$this->generateOverrides($repository);

					//$file = new FileSystem();
					//$file->write(WWW_DIR . '/temp/' . $genClassName . ".php", "<?php\n" . $repository);
					$this->cache->save($genClassName, $repository);
				}
				if (!class_exists($genClassName)) {
					LimitedScope::evaluate("<?php " . $repository);
				}
				//LimitedScope::load(WWW_DIR . '/temp/' . $genClassName . ".php", TRUE);
			}
		}
	}

	/**
	 * @param ClassType $repository
	 */
	private function generateOverrides(ClassType $repository) {
		$repository->addMethod("getReferences")
			->addDocument("@return array")
			->addDocument("@throws Exception\EntityException")
			->setBody("\$references = parent::getReferences();\n\nforeach (\$references as &\$ref) {\n\t\$ref->targetEntity = \"" . EntityManager::PREFIX . "\" . str_replace(\"\\\\\", \"\", \$ref->targetEntity) . \"Entity\";\n}\nreturn \$references;");
	}

	/**
	 * @param array $columns
	 * @param ClassType $repository
	 */
	private function generateGetters(array $columns, ClassType $repository)
	{
		foreach ($columns as $column) {
			$name = $column['name'];
			$method = $repository->addMethod("get" . ucfirst($name))
				->setBody("if (\$this->row && \$this->$name === NULL) {\n" . "\t" . "\$this->$name = \$this->row->$name;\n}\n\nreturn \$this->$name;");
			foreach ($column['annotations'] as $key => $doc) {
				if ($key == "var") {
					$return = implode(" ", $doc);
					$method->addDocument("@return " . $return);
					break;
				}
			}
		}
	}

	/**
	 * @param array $references
	 * @param ClassType $repository
	 */
	private function generateReferences(array $references, ClassType $repository = NULL)
	{
		foreach ($references as $ref) {
			$body = $phpDoc = "";
			$genClassName = EntityManager::PREFIX . str_replace("\\", "", $ref->targetEntity) . "Entity";
			switch ($ref->linkage) {
				case 'OneToMany':
				case 'ManyToMany':
					$this->generateEntity($ref->targetEntity);
					if ($repository) {
						$phpDoc = "@return " . $ref->targetEntity . "[]";
						$body = "if (\$this->row && \$this->$ref->property === NULL) {\n\tif (\$this->row->getTable()->getPrimary(TRUE) === \"" . $ref->key . "\") {\n\t\t\$this->" . $ref->property . " = \$this->oneToMany(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\"";
						$body .= ");\n\t} else {\n\t\t\$this->" . $ref->property ." = \$this->oneToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\");\n\t}\n}\nreturn \$this->" . $ref->property . ";";
					}
					break;
				case 'OneToOne':
				case 'ManyToOne':
					$this->generateEntity($ref->targetEntity);
					if ($repository) {
						$phpDoc = "@return " . $ref->targetEntity;
						$body = "if (\$this->row && \$this->$ref->property === NULL) {\n\tif (\$this->row->getTable()->getPrimary(TRUE) === \"" . $ref->key . "\") {\n\t\t\$this->" . $ref->property . " = \$this->manyToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\"";
						$body .= ");\n\t} else {\n\t\t\$this->" . $ref->property ." = \$this->oneToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\");\n\t}\n}\nreturn \$this->" . $ref->property . ";";
					}
					break;
			}
			if ($repository) {
				$repository->addMethod("get" . ucfirst($ref->property))
					->addDocument($phpDoc)
					->setBody($body);
			}
		}
	}
} 