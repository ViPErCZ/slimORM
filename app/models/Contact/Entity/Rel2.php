<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 11:37
 */

namespace Model\Contact\Entity;


use slimORM\Entity\Entity;

/**
 * Class Rel2
 * @table rel2
 * @package Model\Contact\Entity
 */
class Rel2 extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $rel2ID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @reference rel1
	 * @OneToOne(targetEntity="Model\Contact\Entity\Rel1", mappedBy="rel2ID")
	 * @var Rel1
	 */
	protected $rel1;

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \Model\Contact\Entity\Rel1
	 */
	public function getRel1()
	{
		return $this->rel1;
	}

	/**
	 * @return int
	 */
	public function getRel2ID()
	{
		return $this->rel2ID;
	}

} 