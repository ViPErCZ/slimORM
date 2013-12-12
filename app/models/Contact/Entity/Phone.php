<?php
/**
 * User: Martin
 * Date: 10.12.13
 * Time: 14:26
 */

namespace Model\Contact\Entity;


use slimORM\Entity\Entity;

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

} 