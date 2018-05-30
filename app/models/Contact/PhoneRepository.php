<?php

namespace Model\Contact;

use slimORM\AbstractRepository;
use Model\Contact\Entity\Phone;
use slimORM\EntityManager;

class PhoneRepository extends AbstractRepository {

	/** Entity Class */
	private const ENTITY = Phone::class;

	/**
	 * PhoneRepository constructor.
	 * @param EntityManager $entityManager
	 * @param $entity
	 */
	public function __construct(EntityManager $entityManager, $entity) {
		parent::__construct($entityManager, self::ENTITY);
	}
} 