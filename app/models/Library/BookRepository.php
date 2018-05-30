<?php

namespace Model\Library;

use slimORM\AbstractRepository;
use Model\Library\Entity\Book;
use slimORM\EntityManager;

class BookRepository extends AbstractRepository {

	/** Entity Class */
	private const ENTITY = Book::class;

	/**
	 * BookRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, self::ENTITY);
	}
} 