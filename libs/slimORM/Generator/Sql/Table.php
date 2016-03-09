<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 20.12.15
 * Time: 18:25
 */

namespace slimORM\Generator\Sql;
use slimORM\Generator\SqlParserException;

/**
 * Class Table
 * @package slimORM\Generator\Sql
 */
class Table {

	/** @var string */
	protected $name;

	/** @var array */
	protected $columns = array();

	/** @var array */
	protected $references = array();

	/** @var array */
	protected $related = array();

	/**
	 * Table constructor.
	 * @param $name
	 * @param array $columns
	 * @throws SqlParserException
	 */
	public function __construct($name, array $columns = array()) {
		if (empty($name)) {
			throw new SqlParserException("Name of table cannot be empty.");
		}
		$this->name = $name;

		foreach ($columns as $column) {
			$col = new Column();
			$col->setName($column["name"]);
			$col->setType($this->normalizeTypeHint($column["type"]));
			$col->setPrimary($column["primary"]);
			$col->setNull($column["null"]);
			$this->columns[$column["name"]] = $col;
		}
	}

	/**
	 * @param $type
	 * @return string
	 */
	protected function normalizeTypeHint($type) {
		if ($type == "INT") {
			return "int";
		} else if (strpos($type, "CHAR") !== false || strpos($type, "ENUM") !== false) {
			return "string";
		} else if ($type == "DATETIME") {
			return '\Nette\Utils\DateTime';
		} else {
			return $type;
		}
	}

	/**
	 * @param Column $column
	 */
	public function addColumn(Column $column) {
		$column->setType($this->normalizeTypeHint($column->getType()));
		$this->columns[$column->getName()] = $column;
	}

	/**
	 * @param Reference $reference
	 */
	public function addReference(Reference $reference) {
		$this->references[$reference->getKey()] = $reference;
	}

	/**
	 * @param Reference $related
	 */
	public function addRelated(Reference $related) {
		$this->related[$related->getTable()] = $related;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return Column[]
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return Reference[]
	 */
	public function getReferences() {
		return $this->references;
	}

	/**
	 * @return Reference[]
	 */
	public function getRelated() {
		return $this->related;
	}

	/**
	 * @return string|null
	 */
	public function getPrimaryKey() {
		foreach ($this->getColumns() as $column) {
			if ($column->isPrimary()) {
				return $column->getName();
			}
		}
		return null;
	}
}