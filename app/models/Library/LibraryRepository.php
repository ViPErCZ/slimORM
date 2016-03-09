<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:54
 */

namespace Model\Library;

use slimORM\AbstractRepository;
use Model\Library\Entity\Library;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class LibraryRepository extends AbstractRepository {

	/** Entity Class */
	const ENTITY = '\Model\Library\Entity\Library';

	/**
	 * LibraryRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
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
	 * @param Entity $library
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $library) {
		return parent::push($library);
	}
} 