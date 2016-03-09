<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 7.1.16
 * Time: 13:46
 */

namespace slimORM\Generator;

use Nette\InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\Utils\DateTime;
use slimORM\Generator\Sql\Column;
use slimORM\Generator\Sql\Reference;
use slimORM\Generator\Sql\Table;

/**
 * Class EntityGenerator
 * @package slimORM\Generator
 * @author Martin Chudoba <info@vipersoftware.net>
 */
class EntityGenerator {

	/** @var string */
	protected $path;

	/** @var Table */
	protected $table;

	/** suffix name */
	const suffix = "Entity";

	/** prefix name */
	const prefix = "Base";

	/**
	 * EntityGenerator constructor.
	 * @param string $path
	 * @param Table $table
	 */
	public function __construct($path, Table $table) {
		$this->path = $path;
		$this->table = $table;

		if (!is_dir($this->path)) {
			@mkdir($this->path);
		}
	}

	/**
	 * @return Table
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @param Table $table
	 */
	public function setTable(Table $table) {
		$this->table = $table;
	}

	/**
	 * @return bool
	 */
	public function generate() {
		$tableName = $this->table->getName();
		$className = self::prefix . ucfirst($tableName) . self::suffix;

		$entity = new ClassType($className);
		$entity->addDocument("Class " . $className);
		$current = new DateTime();
		$entity->addDocument("@generated " . $current);
		$entity->addDocument("@table " . $tableName);
		$entity->addExtend('\slimORM\Entity\Entity');
		$entity->setAbstract(true);

		foreach ($this->table->getColumns() as $column) {
			$entity->addProperty($column->getName())
				->setVisibility("protected")
				->setDocuments(array(
					"@column",
					"@var " . $column->getType()
				));
		}

		foreach ($this->table->getReferences() as $reference) {
			$referencePropertyName = self::prefix . ucfirst($reference->getTable()) . self::suffix;
			$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? "true" : "false";
			$entity->addProperty($reference->getTable())
				->setVisibility("protected")
				->setDocuments(array(
					"@reference " . $reference->getTable(),
					"@OneToOne(targetEntity=\"" . $referencePropertyName . "\", mappedBy=\"" . $reference->getKey() . "\", canBeNULL=" . $null . ")",
					"@var " . $referencePropertyName
				));
		}

		foreach ($this->table->getRelated() as $reference) {
			try {
				$entity->getProperty($reference->getTable());
			} catch (InvalidArgumentException $e) {
				$referencePropertyName = self::prefix . ucfirst($reference->getTable()) . self::suffix;
				$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? "true" : "false";
				$entity->addProperty($reference->getTable())
					->setVisibility("protected")
					->setDocuments(array(
						"@reference " . $reference->getTable(),
						"@OneToMany(targetEntity=\"" . $referencePropertyName . "\", mappedBy=\"" . $reference->getKey() . "\", canBeNULL=" . $null . ")",
						"@var " . $referencePropertyName
					));
			}
		}

		foreach ($this->table->getColumns() as $column) {
			$this->generateSetters($entity, $column);
			$this->generateGetters($entity, $column);
		}

		foreach ($this->table->getReferences() as $reference) {
			$this->generateReferences($entity, $reference);
		}

		foreach ($this->table->getRelated() as $reference) {
			$this->generateRelated($entity, $reference);
		}

		$this->generateChild();

		return $this->save($className, $entity);
	}

	/**
	 * @return bool
	 */
	protected function generateChild() {
		$tableName = ucfirst($this->table->getName());
		$parentClassName = self::prefix . $tableName . self::suffix;

		$entity = new ClassType($tableName);
		$entity->addDocument("Class " . $tableName);
		$current = new DateTime();
		$entity->addDocument("@generated " . $current);
		$entity->addDocument("@table " . $this->table->getName());
		$entity->addExtend('\\' . $parentClassName);
		$entity->setAbstract(false);

		$handle = null;
		@mkdir($this->path . "/" . $tableName);
		$filePath = $this->path . "/" . $tableName . "/" . $tableName . ".php";
		if (!is_file($filePath)) {
			if (($handle = @fopen($filePath, "w")) !== null) {
				fwrite($handle, "<?php\n\n");
				fwrite($handle, $entity);
				fclose($handle);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * @param ClassType $entity
	 * @param Column $column
	 */
	protected function generateSetters(ClassType $entity, Column $column) {
		$name = $column->getName();
		$parameter = new Parameter($name);

		if ($column->isNull()) {
			$parameter->setOptional(true);
			$parameter->setDefaultValue(null);
		}

		$entity->addMethod("set" . ucfirst($column->getName()))
			->setDocuments(array("@param " . $column->getType() . " $" . $name, "@return \$this"))
			->setParameters(array($parameter))
			->setBody("\$this->" . $name . " = \$" . $name . ";\nreturn \$this;");
	}

	/**
	 * @param ClassType $entity
	 * @param Column $column
	 */
	protected function generateGetters(ClassType $entity, Column $column) {
		$name = $column->getName();
		$methodName = "get" . ucfirst($column->getName());

		$entity->addMethod($methodName)
			->setDocuments(array("@return " . $column->getType()))
			->setBody("return \$this->" . $name . ";");
	}

	/**
	 * @param ClassType $entity
	 * @param Reference $reference
	 */
	protected function generateReferences(ClassType $entity, Reference $reference) {
		$name = $reference->getTable();
		$methodSetName = "set" . ucfirst($name);
		$methodGetName = "get" . ucfirst($name);
		$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? "true" : "false";

		$parameter = new Parameter($name);
		$parameter->setTypeHint("\\" . self::prefix . ucfirst($name) . self::suffix);

		if ($null) {
			$parameter->setOptional(true);
			$parameter->setDefaultValue(null);
		}

		$entity->addMethod($methodSetName)
			->setDocuments(array("@var " . "\\" . self::prefix . ucfirst($name) . self::suffix . " $" . $name))
			->setParameters(array($parameter))
			->setBody("\$this->" . $name . " = $" . $name . ";");

		$entity->addMethod($methodGetName)
			->setDocuments(array("@return " . "\\" . self::prefix . ucfirst($name) . self::suffix . " $" . $name))
			->setBody("return \$this->" . $name . ";");
	}

	/**
	 * @param ClassType $entity
	 * @param Reference $reference
	 */
	protected function generateRelated(ClassType $entity, Reference $reference) {
		try {
			$entity->getMethod("get" . ucfirst($reference->getTable()));
		} catch (InvalidArgumentException $e) {
			$name = $reference->getTable();
			$methodSetName = "add" . ucfirst($name);
			$methodGetName = "get" . ucfirst($name);

			$parameter = new Parameter($name);
			$parameter->setTypeHint("\\" . self::prefix . ucfirst($name) . self::suffix);

			$entity->addMethod($methodSetName)
				->setDocuments(array("@var " . "\\" . self::prefix . ucfirst($name) . self::suffix . " $" . $name))
				->setParameters(array($parameter))
				->setBody("\$this->" . $name . " = $" . $name . ";");

			$entity->addMethod($methodGetName)
				->setDocuments(array("@return " . "\\" . ucfirst($name) . "Repository $" . $name))
				->setBody("return \$this->" . $name . ";");
		}
	}

	/**
	 * @param $className
	 * @param ClassType $entity
	 * @return bool
	 */
	protected function save($className, ClassType $entity) {
		$handle = null;
		if (($handle = @fopen($this->path . "/" . $className . ".php", "w")) !== null) {
			fwrite($handle, "<?php\n\n");
			fwrite($handle, $entity);
			fclose($handle);
			return true;
		} else {
			return false;
		}
	}
}