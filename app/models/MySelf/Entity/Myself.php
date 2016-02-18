<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 13:36
 */

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
	 * @param int $childID
	 */
	public function setChildID($childID)
	{
		$this->childID = $childID;
	}

	/**
	 * @param int $myselfID
	 */
	public function setMyselfID($myselfID)
	{
		$this->myselfID = $myselfID;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @param \Model\MySelf\Entity\Myself $child
	 */
	public function setChild(Myself $child = NULL)
	{
		$this->child = $child;
	}

	/**
	 * @return \Model\MySelf\Entity\Myself
	 */
	public function getChild()
	{
		return $this->child;
	}

	/**
	 * @return int
	 */
	public function getChildID()
	{
		return $this->childID;
	}

	/**
	 * @return int
	 */
	public function getMyselfID()
	{
		return $this->myselfID;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

} 