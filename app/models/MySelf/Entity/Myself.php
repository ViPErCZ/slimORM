<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 13:36
 */

namespace Model\MySelf\Entity;


use slimORM\Entity\Entity;

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
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @param \Model\MySelf\Entity\Myself $child
	 */
	public function setChild(Myself $child)
	{
		$this->child = $child;
	}

} 