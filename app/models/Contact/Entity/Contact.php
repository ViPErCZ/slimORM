<?php
/**
 * User: Martin
 * Date: 10.12.13
 * Time: 14:24
 */

namespace Model\Contact\Entity;


use Model\Contact\PhoneRepository;
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
	 * @param int $contactID
	 */
	public function setContactID($contactID)
	{
		$this->contactID = $contactID;
	}

	/**
	 * @param int $rel1ID
	 */
	public function setRel1ID($rel1ID)
	{
		$this->rel1ID = $rel1ID;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->address = (string)$address;
	}

	/**
	 * Add Phone
	 * @param Phone $phone
	 */
	public function addPhone(Phone $phone) {
		if ($this->phones === NULL) {
			$this->phones = clone $this->entityManager->getRepository('\Model\Contact\Entity\Phone');
			if ($this->toRow() === NULL) {
				$this->phones->clear();
			}
		}
		$this->phones->push($phone);
	}

	/**
	 * @param \Model\Contact\Entity\Rel1 $rel1
	 */
	public function setRel1(Rel1 $rel1 = NULL)
	{
		$this->rel1 = $rel1;
	}

	/**
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @return int
	 */
	public function getContactID()
	{
		return $this->contactID;
	}

	/**
	 * @return PhoneRepository
	 */
	public function getPhones() {
		return $this->phones;
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
	public function getRel1ID()
	{
		return $this->rel1ID;
	}

} 