<?php

namespace Model\MySelf\Entity;

use slimORM\Entity\Entity;

/**
 * Class Myself
 * @table myself
 * @package Model\MySelf\Entity
 */
class Myself extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $myselfID;

	/**
	 * @column
	 * @var int
	 */
	protected $childID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @reference myself
	 * @OneToOne(targetEntity="\Model\MySelf\Entity\Myself", mappedBy="childID", canBeNULL=TRUE)
	 * @var Myself
	 */
	protected $child;

	/**
	 * @return Myself|null
	 */
	public function getChild(): ?Myself {
		return $this->child;
	}

	/**
	 * @param Myself $child
	 */
	public function setChild(Myself $child = null): void {
		$this->child = $child;
	}

	/**
	 * @return int|null
	 */
	public function getChildID(): ?int {
		return $this->childID;
	}

	/**
	 * @param int $childID
	 */
	public function setChildID($childID): void {
		$this->childID = $childID;
	}

	/**
	 * @return int|null
	 */
	public function getMyselfID(): ?int {
		return $this->myselfID;
	}

	/**
	 * @param int $myselfID
	 */
	public function setMyselfID($myselfID): void {
		$this->myselfID = $myselfID;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name): void {
		$this->name = (string)$name;
	}

} 