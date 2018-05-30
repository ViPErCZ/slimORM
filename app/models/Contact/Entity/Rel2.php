<?php

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
	 * @return Rel1|null
	 */
	public function getRel1(): ?Rel1 {
		return $this->rel1;
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