<?php

namespace slimORM\Generator;

use Nette\InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\Utils\DateTime;
use slimORM\Generator\Sql\Column;
use slimORM\Generator\Sql\Reference;
use slimORM\Generator\Sql\Table;
use slimORM\Entity\Entity;

/**
 * Class EntityGenerator
 * @package slimORM\Generator
 * @author Martin Chudoba <info@vipersoftware.net>
 */
class EntityGenerator implements IGenerateAble {

	/** @var string */
	protected $path;

	/** @var Table */
	protected $table;

	/** suffix name */
	const suffix = 'Entity';

	/** prefix name */
	const prefix = 'Base';

	/**
	 * EntityGenerator constructor.
	 * @param string $path
	 * @param Table $table
	 * @throws \RuntimeException
	 */
	public function __construct($path, Table $table) {
		$this->path = $path;
		$this->table = $table;

		if (!is_dir($this->path) && !mkdir($this->path) && !is_dir($this->path)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
		}
	}

	/**
	 * @return Table
	 */
	public function getTable(): Sql\Table {
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
	 * @throws \RuntimeException
	 */
	public function generate(): bool {
		$tableName = $this->table->getName();
		$className = self::prefix . ucfirst($tableName) . self::suffix;

		$entity = new ClassType($className);
		$entity->addComment('Class ' . $className);
		$current = new DateTime();
		$entity->addComment('@generated ' . $current);
		$entity->addComment('@table ' . $tableName);
		$entity->addExtend(Entity::class);
		$entity->setAbstract();

		foreach ($this->table->getColumns() as $column) {
			$entity->addProperty($column->getName())
				->setVisibility('protected')
				->addComment('@column')
				->addComment('@var ' . $column->getType());
		}

		foreach ($this->table->getReferences() as $reference) {
			$referencePropertyName = self::prefix . ucfirst($reference->getTable()) . self::suffix;
			$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? 'true' : 'false';
			$entity->addProperty($reference->getTable())
				->setVisibility('protected')
				->addComment('@reference ' . $reference->getTable())
				->addComment('@OneToOne(targetEntity="' . $referencePropertyName . '", mappedBy="' . $reference->getKey() . '", canBeNULL=' . $null . ')')
				->addComment('@var ' . $referencePropertyName);
		}

		foreach ($this->table->getRelated() as $reference) {
			try {
				$entity->getProperty($reference->getTable());
			} catch (InvalidArgumentException $e) {
				$referencePropertyName = self::prefix . ucfirst($reference->getTable()) . self::suffix;
				$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? 'true' : 'false';
				$entity->addProperty($reference->getTable())
					->setVisibility('protected')
					->addComment('@reference ' . $reference->getTable())
					->addComment('@OneToMany(targetEntity="' . $referencePropertyName . '", mappedBy="' . $reference->getKey() . '", canBeNULL=' . $null . ')')
					->addComment('@var ' . $referencePropertyName);
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
	 * @throws \RuntimeException
	 */
	protected function generateChild(): bool {
		$tableName = ucfirst($this->table->getName());
		$parentClassName = self::prefix . $tableName . self::suffix;

		$entity = new ClassType($tableName);
		$entity->addComment('Class ' . $tableName);
		$current = new DateTime();
		$entity->addComment('@generated ' . $current);
		$entity->addComment('@table ' . $this->table->getName());
		$entity->addExtend('\\' . $parentClassName);
		$entity->setAbstract(false);

		$handle = null;
		if (!is_dir($this->path . '/' . $tableName) && !mkdir($this->path . '/' . $tableName) && !is_dir($this->path . '/' . $tableName)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path . '/' . $tableName));
		}
		$filePath = $this->path . '/' . $tableName . '/' . $tableName . '.php';
		if (!is_file($filePath)) {
			if (($handle = @fopen($filePath, 'w')) !== null) {
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
			$parameter->setDefaultValue(null);
		}

		$entity->addMethod('set' . ucfirst($column->getName()))
			->setComment(implode(PHP_EOL, array('@param ' . $column->getType() . ' $' . $name, '@return $this')))
			->setParameters(array($parameter))
			->setBody('$this->' . $name . ' = $' . $name . ";\nreturn \$this;");
	}

	/**
	 * @param ClassType $entity
	 * @param Column $column
	 */
	protected function generateGetters(ClassType $entity, Column $column) {
		$name = $column->getName();
		$methodName = 'get' . ucfirst($column->getName());

		$method = $entity->addMethod($methodName)
			->addComment('@return ' . $column->getType())
			->setBody('return $this->' . $name . ';');

		if (PHP_VERSION_ID >= 70100) {
			$method->setReturnType($column->getType());
			$method->setReturnNullable();
		}
	}

	/**
	 * @param ClassType $entity
	 * @param Reference $reference
	 */
	protected function generateReferences(ClassType $entity, Reference $reference) {
		$name = $reference->getTable();
		$methodSetName = 'set' . ucfirst($name);
		$methodGetName = 'get' . ucfirst($name);
		$null = $this->table->getColumns()[$reference->getKey()]->isNull() ? 'true' : 'false';

		$parameter = new Parameter($name);
		$parameter->setTypeHint("\\" . self::prefix . ucfirst($name) . self::suffix);

		if ($null) {
			//$parameter->setOptional(true);
			$parameter->setDefaultValue(null);
		}

		$entity->addMethod($methodSetName)
			->addComment('@var ' . "\\" . self::prefix . ucfirst($name) . self::suffix . ' $' . $name)
			->setParameters(array($parameter))
			->setBody('$this->' . $name . ' = $' . $name . ';');

		$method = $entity->addMethod($methodGetName)
			->addComment('@return ' . "\\" . self::prefix . ucfirst($name) . self::suffix . ' $' . $name)
			->setBody('return $this->' . $name . ';');

		if (PHP_VERSION_ID >= 70100) {
			$method->setReturnType("\\" . self::prefix . ucfirst($name) . self::suffix);
			$method->setReturnNullable();
		}
	}

	/**
	 * @param ClassType $entity
	 * @param Reference $reference
	 */
	protected function generateRelated(ClassType $entity, Reference $reference) {
		try {
			$entity->getMethod('get' . ucfirst($reference->getTable()));
		} catch (InvalidArgumentException $e) {
			$name = $reference->getTable();
			$methodSetName = 'add' . ucfirst($name);
			$methodGetName = 'get' . ucfirst($name);

			$parameter = new Parameter($name);
			$parameter->setTypeHint("\\" . self::prefix . ucfirst($name) . self::suffix);

			$entity->addMethod($methodSetName)
				->addComment('@var ' . "\\" . self::prefix . ucfirst($name) . self::suffix . ' $' . $name)
				->setParameters(array($parameter))
				->setBody('$this->' . $name . ' = $' . $name . ';');

			$method = $entity->addMethod($methodGetName)
				->addComment('@return ' . "\\" . ucfirst($name) . 'Repository $' . $name)
				->setBody('return $this->' . $name . ';');

			if (PHP_VERSION_ID >= 70100) {
				$method->setReturnType("\\" . ucfirst($name) . 'Repository');
				$method->setReturnNullable();
			}
		}
	}

	/**
	 * @param $className
	 * @param ClassType $entity
	 * @return bool
	 */
	protected function save($className, ClassType $entity): bool {
		$handle = null;
		if (($handle = @fopen($this->path . '/' . $className . '.php', 'w')) !== null) {
			fwrite($handle, "<?php\n\n");
			fwrite($handle, $entity);
			fclose($handle);
			return true;
		} else {
			return false;
		}
	}
}