<?php
/**
 * User: Martin
 * Date: 13.12.13
 * Time: 7:48
 */

namespace Model\Contact;


use slimORM\AbstractRepository;
use Model\Contact\Entity\Contact;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class ContactRepository extends AbstractRepository {

	const ENTITY = '\Model\Contact\Entity\Contact';

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
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
	 * @param Entity $entity
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return Contact[]
	 */
	public function fetchAll() {
		return parent::fetchAll();
	}
} 