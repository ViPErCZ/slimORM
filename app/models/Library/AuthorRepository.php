<?php

namespace Model\Library;

use Model\Library\Entity\Author;
use slimORM\AbstractRepository;
use slimORM\EntityManager;

class AuthorRepository extends AbstractRepository {

	private const ENTITY = Author::class;

	/**
	 * AuthorRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, self::ENTITY);
	}

} 