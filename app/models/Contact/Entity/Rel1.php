<?php

namespace Model\Contact\Entity;

use slimORM\Entity\Entity;

/**
 * Class Rel1
 * @table rel1
 * @package Model\Contact\Entity
 */
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

	/**
	 * @return int|null
	 */
	public function getRel1ID(): ?int {
		return $this->rel1ID;
	}

	/**
	 * @param int $rel1ID
	 */
	public function setRel1ID($rel1ID): void {
		$this->rel1ID = $rel1ID;
	}

	/**
	 * @return Rel2|null
	 */
	public function getRel2(): ?Rel2 {
		return $this->rel2;
	}

	/**
	 * @param Rel2 $rel2
	 */
	public function setRel2(Rel2 $rel2): void {
		$this->rel2 = $rel2;
	}

	/**
	 * @return int|null
	 */
	public function getRel2ID(): ?int {
		return $this->rel2ID;
	}

	/**
	 * @param int $rel2ID
	 */
	public function setRel2ID($rel2ID): void {
		$this->rel2ID = $rel2ID;
	}

} 