<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:54
 */

namespace Model\Library;

use Model\Base\BaseModel;
use Model\Library\Entity\Library;
use Nette\Utils\Paginator;
use slimORM\EntityManager;

class LibraryRepository extends BaseModel {

	/** Entity Class */
	const ENTITY = "\Model\Library\Entity\Library";

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = LibraryRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Library|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return LibraryRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return Library|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Library $library
	 * @return LibraryRepository
	 */
	public function push(Library $library) {
		return parent::push($library);
	}
} 