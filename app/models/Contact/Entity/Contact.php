<?php

namespace Model\Contact\Entity;

use Model\Contact\PhoneRepository;
use slimORM\BaseRepository;
use slimORM\Entity\Entity;

/**
 * Class Contact
 * @table contact
 * @package Model\Contact\Entity
 */
class Contact extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $contactID;

	/**
	 * @column
	 * @var int
	 */
	protected $rel1ID;

	/**
	 * @reference rel1
	 * @OneToOne(targetEntity="Model\Contact\Entity\Rel1", mappedBy="rel1ID")
	 * @var \Model\Contact\Entity\Rel1
	 */
	protected $rel1;

	/**
	 * @column
	 * @var string
	 */
	protected $address;

	/**
	 * @reference phone
	 * @OneToMany(targetEntity="Model\Contact\Entity\Phone", mappedBy="contactID")
	 * @var PhoneRepository
	 */
	protected $phones;

	/**
	 * @param Phone $phone
	 */
	public function addPhones(Phone $phone): void {
		$this->phones[] = $phone;
	}

	/**
	 * @return string|null
	 */
	public function getAddress(): ?string {
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address): void {
		$this->address = (string)$address;
	}

	/**
	 * @return int|null
	 */
	public function getContactID(): ?int {
		return $this->contactID;
	}

	/**
	 * @param int $contactID
	 */
	public function setContactID($contactID): void {
		$this->contactID = $contactID;
	}

	/**
	 * @return PhoneRepository|null
	 */
	public function getPhones(): ?BaseRepository {
		return $this->phones;
	}

	/**
	 * @return Rel1|null
	 */
	public function getRel1(): ?Rel1 {
		return $this->rel1;
	}

	/**
	 * @param Rel1|null $rel1
	 */
	public function setRel1(Rel1 $rel1 = null): void {
		$this->rel1 = $rel1;
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

} 