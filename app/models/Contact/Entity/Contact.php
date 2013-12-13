<?php
/**
 * User: Martin
 * Date: 10.12.13
 * Time: 14:24
 */

namespace Model\Contact\Entity;


use slimORM\Entity\Entity;

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
	 * @var array
	 */
	protected $phones;

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
		$this->phones[] = $phone;
	}

	/**
	 * @param \Model\Contact\Entity\Rel1 $rel1
	 */
	public function setRel1(Rel1 $rel1)
	{
		$this->rel1 = $rel1;
	}

} 