<?php
/**
 * User: Martin
 * Date: 10.12.13
 * Time: 14:26
 */

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
	 * @param string $number
	 */
	public function setNumber($number) {
		$this->number = (string)$number;
	}

	/**
	 * @return \Model\Contact\Entity\Contact
	 */
	public function getContact()
	{
		return $this->contact;
	}

	/**
	 * @return int
	 */
	public function getContactID()
	{
		return $this->contactID;
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @return int
	 */
	public function getPhoneID()
	{
		return $this->phoneID;
	}

} 