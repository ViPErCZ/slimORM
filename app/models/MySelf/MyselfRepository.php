<?php

namespace Model\MySelf;

use slimORM\AbstractRepository;
use Model\MySelf\Entity\Myself;
use slimORM\EntityManager;

class MyselfRepository extends AbstractRepository {

	private const ENTITY = Myself::class;

	/**
	 * MyselfRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, self::ENTITY);
	}
} 