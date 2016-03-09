<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 6.1.16
 * Time: 15:55
 */

namespace slimORM\Generator\Sql;

/**
 * Class Column
 * @package slimORM\Generator\Sql
 */
class Column {

	/** @var string */
	protected $name;

	/** @var string */
	protected $type;

	/** @var string */
	protected $note;

	/** @var bool */
	protected $null = true;

	/** @var bool */
	protected $primary = false;

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @param mixed $note
	 */
	public function setNote($note) {
		$this->note = $note;
	}

	/**
	 * @return boolean
	 */
	public function isPrimary() {
		return $this->primary;
	}

	/**
	 * @param boolean $primary
	 */
	public function setPrimary($primary) {
		$this->primary = $primary;
	}

	/**
	 * @return boolean
	 */
	public function isNull() {
		return $this->null;
	}

	/**
	 * @param boolean $null
	 */
	public function setNull($null) {
		$this->null = $null;
	}

}