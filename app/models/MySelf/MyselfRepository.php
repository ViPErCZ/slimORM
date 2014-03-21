<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 13:38
 */

namespace Model\MySelf;


use Model\Base\BaseModel;
use Model\MySelf\Entity\Myself;
use Nette\Database\Context;
use Nette\Utils\Paginator;
use slimORM\BaseRepository;

class MyselfRepository extends BaseModel {

	const ENTITY = '\Model\Myself\Entity\Myself';

	/**
	 * @param string $key
	 * @return \Model\Myself\Entity\Myself|NULL
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository(MyselfRepository::ENTITY);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return MyselfRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository(MyselfRepository::ENTITY);
		return $repository->read($paginator);
	}

	/**
	 * @param bool $needTransaction
	 * @param Myself $entity
	 * @return Myself|TRUE
	 */
	public function save($needTransaction = TRUE, Myself $entity = NULL) {
		$repository = $this->entityManager->getRepository(MyselfRepository::ENTITY);
		return $repository->save($needTransaction, $entity);
	}

	/**
	 * @param Myself $myself
	 * @return BaseRepository
	 */
	public function push(Myself $myself) {
		$repository = $this->entityManager->getRepository(MyselfRepository::ENTITY);
		$repository->push($myself);
		return $repository;
	}
} 