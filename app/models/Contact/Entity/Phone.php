<?php

namespace Model\Contact\Entity;

use slimORM\Entity\Entity;

/**
 * Class Phone
 * @table phone
 * @package Model\Contact\Entity
 */
class Phone extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $phoneID;

	/**
	 * @column
	 * @var int
	 */
	protected $contactID;

	/**
	 * @column
	 * @var string
	 */
	protected $number;

	/**
	 * @reference contact
	 * @OneToOne(targetEntity="Model\Contact\Entity\Contact", mappedBy="contactID")
	 * @var Contact
	 */
	protected $contact;

	/**
	 * @return Contact|null
	 */
	public function getContact(): ?Contact {
		return $this->contact;
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
	 * @return string|null
	 */
	public function getNumber(): ?string {
		return $this->number;
	}

	/**
	 * @param string $number
	 */
	public function setNumber($number): void {
		$this->number = (string)$number;
	}

	/**
	 * @return int|null
	 */
	public function getPhoneID(): ?int {
		return $this->phoneID;
	}

	/**
	 * @param int $phoneID
	 */
	public function setPhoneID($phoneID): void {
		$this->phoneID = $phoneID;
	}

} 