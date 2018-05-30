<?php

namespace Model\Library;

use slimORM\AbstractRepository;
use Model\Library\Entity\BookAttachment;

class BookAttachmentRepository extends AbstractRepository {

	/** Entity Class */
	private const ENTITY = BookAttachment::class;

	/**
	 * BookAttachmentRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($this->entityManager, self::ENTITY);
	}
} 