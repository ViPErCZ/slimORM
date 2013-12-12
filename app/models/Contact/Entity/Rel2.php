<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 11:37
 */

namespace Model\Contact\Entity;


use slimORM\Entity\Entity;

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

} 