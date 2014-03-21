<?php
/**
 * User: Martin Chudoba
 * Date: 9.3.14
 * Time: 13:09
 */

namespace slimORM;


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

	/** @var \slimORM\Reflexion\EntityReflexion */
	private $reflexion;

	/** @var array */
	private $entities;

	/**
	 * Constructor
	 * @param Context $connection
	 */
	public function __construct(Context $connection)
	{
		$this->repositories = array();
		$this->entities = array();
		$this->reflexion = new EntityReflexion();
		$this->connection = $connection;
	}

	/**
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 * @return \slimORM\BaseRepository
	 */
	public function getRepository($className)
	{
		if (array_key_exists($className, $this->repositories)) {
			return $this->repositories[$className];
		} else {
			if (class_exists($className)) {
				$this->generateEntity($className);
				$this->generateRepository($className);
				return $this->repositories[$className];
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
		return $this->reflexion->getColumns($className);
	}

	/**
	 * @param string $className
	 * @return array
	 */
	final private function getReferences($className)
	{
		return $this->reflexion->getReferences($className);
	}

	/**
	 * Generate repository class
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 */
	private function generateRepository($className)
	{
		$reflexion = \Nette\Reflection\ClassType::from($className);
		if ($reflexion->hasAnnotation("table")) {
			$table = $reflexion->getAnnotation("table");
		} else {
			throw new RepositoryException("Entity \"" . $className . " has no annotation \"table\"");
		}
		$genClassName = EntityManager::PREFIX . str_replace("\\", "", $reflexion->getName()) . "Repository";
		$repository = new ClassType($genClassName);
		$repository->addExtend("\slimORM\BaseRepository");
		$repository->setFinal(TRUE);
		$repository->addDocument(str_replace("\\", "", $reflexion->getName()) . " Repository");
		$repository->addProperty("connection")
			->setVisibility("protected")
			->setDocuments(array("@var \Nette\Database\Context"));
		$parameter = new Parameter();
		$parameter->setName("connection");
		$parameter->setTypeHint("\Nette\Database\Context");
		$entity = EntityManager::PREFIX . str_replace("\\", "", $reflexion->getName()) . "Entity";
		$repository->addMethod("__construct")
			->setParameters(array($parameter))
			->setBody("\$this->connection = \$connection;\nparent::__construct(\$connection, \"$table\", \"$entity\");");
		$parameter = new Parameter();
		$parameter->setName("key");
		$repository->addMethod("get")
			->setDocuments(array("Find item by primary key", "@param int \$key", "@return $entity|null"))
			->setParameters(array($parameter))
			->setBody("return parent::get(\$key);");
		//echo $repository;
		$file = new FileSystem();
		$file->write(WWW_DIR . '/temp/' . $genClassName . ".php", "<?php\n" . $repository);
		LimitedScope::load(WWW_DIR . '/temp/' . $genClassName . ".php", TRUE);
		$this->repositories[$className] = new $genClassName($this->connection);
	}

	/**
	 * Generate entity class
	 * @param $className
	 * @throws Exceptions\RepositoryException
	 */
	private function generateEntity($className)
	{
		$reflexion = \Nette\Reflection\ClassType::from($className);
		$genClassName = EntityManager::PREFIX . str_replace("\\", "", $reflexion->getName()) . "Entity";
		if ($reflexion->hasAnnotation("table")) {
			$table = $reflexion->getAnnotation("table");
		} else {
			throw new RepositoryException("Entity \"" . $className . " has no annotation \"table\"");
		}
		if (in_array($genClassName, $this->entities)) {
			return;
		} else {
			$this->entities[$genClassName] = $genClassName;
			//if (!file_exists(WWW_DIR . '/temp/' . $genClassName . ".php")) {
				$repository = new ClassType($genClassName);
				$repository->addExtend($reflexion->getName());
				$repository->setFinal(TRUE);
				$repository->addDocument(str_replace("\\", "", $reflexion->getName()) . " Entity");
				$repository->addDocument("@table " . $table);

				$columns = $this->getColumns($className);
				$this->generateGetters($columns, $repository);

				$references = $this->getReferences($className);
				$this->generateReferences($references, $repository);

				$this->generateOverrides($repository);

				$file = new FileSystem();
				$file->write(WWW_DIR . '/temp/' . $genClassName . ".php", "<?php\n" . $repository);
			//}
			LimitedScope::load(WWW_DIR . '/temp/' . $genClassName . ".php", TRUE);
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
	private function generateReferences(array $references, ClassType $repository)
	{
		foreach ($references as $ref) {
			$body = $phpDoc = "";
			$genClassName = EntityManager::PREFIX . str_replace("\\", "", $ref->targetEntity) . "Entity";
			switch ($ref->linkage) {
				case 'OneToMany':
				case 'ManyToMany':
					$this->generateEntity($ref->targetEntity);
					$phpDoc = "@return " . $ref->targetEntity . "[]";
					$body = "if (\$this->row && \$this->$ref->property === NULL) {\n\tif (\$this->row->getTable()->getPrimary(TRUE) === \"" . $ref->key . "\") {\n\t\t\$this->" . $ref->property . " = \$this->oneToMany(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\"";
					$body .= ");\n\t} else {\n\t\t\$this->" . $ref->property ." = \$this->oneToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\");\n\t}\n}\nreturn \$this->" . $ref->property . ";";
					break;
				case 'OneToOne':
				case 'ManyToOne':
					$this->generateEntity($ref->targetEntity);
					$phpDoc = "@return " . $ref->targetEntity;
					$body = "if (\$this->row && \$this->$ref->property === NULL) {\n\tif (\$this->row->getTable()->getPrimary(TRUE) === \"" . $ref->key . "\") {\n\t\t\$this->" . $ref->property . " = \$this->manyToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\"";
					$body .= ");\n\t} else {\n\t\t\$this->" . $ref->property ." = \$this->oneToOne(\"" . $ref->property . "\", \"" . $ref->table . "\", \"" . $ref->key . "\", \"" . $genClassName . "\");\n\t}\n}\nreturn \$this->" . $ref->property . ";";
					break;
			}
			$repository->addMethod("get" . ucfirst($ref->property))
				->addDocument($phpDoc)
				->setBody($body);
		}
	}
} 