<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 7.1.16
 * Time: 8:52
 */

namespace slimORM\Generator\Sql;

/**
 * Class Reference
 * @package slimORM\Generator\Sql
 */
class Reference {

	/** @var string */
	protected $key;

	/** @var string */
	protected $table;

	/** @var string */
	protected $columnName;

	/**
	 * Reference constructor.
	 * @param string $key
	 * @param string $columnName
	 * @param string $table
	 */
	public function __construct($key, $columnName, $table) {
		$this->key = $key;
		$this->columnName = $columnName;
		$this->table = $table;
	}

	/**
	 * @return mixed
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @param mixed $key
	 */
	public function setKey($key) {
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getColumnName() {
		return $this->columnName;
	}

	/**
	 * @param string $columnName
	 */
	public function setColumnName($columnName) {
		$this->columnName = $columnName;
	}

	/**
	 * @return mixed
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @param mixed $table
	 */
	public function setTable($table) {
		$this->table = $table;
	}
}