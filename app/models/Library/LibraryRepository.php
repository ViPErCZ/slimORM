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

	/**
	 * @param string $key
	 * @return \Model\Library\Entity\Library|NULL
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository(LibraryRepository::ENTITY);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return LibraryRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository(LibraryRepository::ENTITY);
		return $repository->read($paginator);
	}

	/**
	 * @param bool $needTransaction
	 * @param Library $library
	 * @return Library|TRUE
	 */
	public function save($needTransaction = TRUE, Library $library = NULL) {
		$repository = $this->entityManager->getRepository(LibraryRepository::ENTITY);
		return $repository->save($needTransaction, $library);
	}
} 