<?php
/**
 * User: Martin
 * Date: 6.12.13
 * Time: 13:20
 */

namespace Model\Library;


use Model\Base\BaseModel;
use Model\Library\Entity\Author;
use Nette\Utils\Paginator;

class AuthorRepository extends BaseModel {

	const ENTITY = 'Model\Library\Entity\Author';

	/**
	 * @param string $key
	 * @return \Model\Library\Entity\Author|NULL
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository(AuthorRepository::ENTITY);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return AuthorRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository(AuthorRepository::ENTITY);
		return $repository->read($paginator);
	}

	/**
	 * @param bool $needTransaction
	 * @param Author $author
	 * @return Author|TRUE
	 */
	public function save($needTransaction = TRUE, Author $author = NULL) {
		$repository = $this->entityManager->getRepository(AuthorRepository::ENTITY);
		return $repository->save($needTransaction, $author);
	}
} 