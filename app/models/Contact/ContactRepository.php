<?php
/**
 * User: Martin
 * Date: 13.12.13
 * Time: 7:48
 */

namespace Model\Contact;


use Model\Base\BaseModel;
use Model\Contact\Entity\Contact;
use Nette\Database\Context;
use Nette\Utils\Paginator;
use slimORM\BaseRepository;
use slimORM\EntityManager;

class ContactRepository extends BaseModel {

	const ENTITY = '\Model\Contact\Entity\Contact';

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = ContactRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Contact|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ContactRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return Contact|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Contact $entity
	 * @return ContactRepository
	 */
	public function push(Contact $entity) {
		return parent::push($entity);
	}
} 