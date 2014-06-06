<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 6.6.14
 * Time: 15:28
 */

namespace Model\Contact;


use Model\Base\BaseModel;
use Model\Contact\Entity\Phone;

class PhoneRepository extends BaseModel {
	/** Entity Class */
	const ENTITY = "\Model\Contact\Entity\Phone";

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = PhoneRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Phone|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return PhoneRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return Phone|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Phone $library
	 * @return PhoneRepository
	 */
	public function push(Phone $book) {
		return parent::push($book);
	}

	/**
	 * @return Phone[]
	 */
	public function fetchAll() {
		return parent::fetchAll();
	}
} 