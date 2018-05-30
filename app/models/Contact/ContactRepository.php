<?php

namespace Model\Contact;

use slimORM\AbstractRepository;
use Model\Contact\Entity\Contact;
use slimORM\EntityManager;

class ContactRepository extends AbstractRepository {

	private const ENTITY = Contact::class;

	/**
	 * ContactRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, self::ENTITY);
	}
}