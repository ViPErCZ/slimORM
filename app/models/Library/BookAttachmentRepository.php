<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 6.6.14
 * Time: 14:58
 */

namespace Model\Library;


use Model\Base\BaseModel;
use Model\Library\Entity\BookAttachment;

class BookAttachmentRepository extends BaseModel {
	/** Entity Class */
	const ENTITY = '\Model\Library\Entity\BookAttachment';

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = BookAttachmentRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return BookAttachment|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return BookAttachmentRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return BookAttachment|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param BookAttachment $library
	 * @return BookAttachmentRepository
	 */
	public function push(BookAttachment $book) {
		return parent::push($book);
	}

	/**
	 * @return BookAttachment[]
	 */
	public function fetchAll() {
		return parent::fetchAll();
	}
} 