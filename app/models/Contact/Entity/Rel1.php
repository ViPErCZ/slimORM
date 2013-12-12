<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 11:35
 */

namespace Model\Contact\Entity;


use slimORM\Entity\Entity;

class Rel1 extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $rel1ID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @column
	 * @var int
	 */
	protected $rel2ID;

	/**
	 * @reference rel2
	 * @OneToOne(targetEntity="Model\Contact\Entity\Rel2", mappedBy="rel2ID")
	 * @var Rel2
	 */
	protected $rel2;

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @param int $rel2ID
	 */
	public function setRel2ID($rel2ID)
	{
		$this->rel2ID = $rel2ID;
	}

	/**
	 * @param \Model\Contact\Entity\Rel2 $rel2
	 */
	public function setRel2(Rel2 $rel2)
	{
		$this->rel2 = $rel2;
	}

} 