<?php

namespace Model\Library;

use slimORM\AbstractRepository;
use Model\Library\Entity\Library;
use slimORM\EntityManager;

class LibraryRepository extends AbstractRepository {

	private const ENTITY = Library::class;

	/**
	 * LibraryRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, self::ENTITY);
	}
} 