<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 13:38
 */

namespace Model\MySelf;


use slimORM\AbstractRepository;
use Model\MySelf\Entity\Myself;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class MyselfRepository extends AbstractRepository {

	const ENTITY = '\Model\Myself\Entity\Myself';

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->entity = MyselfRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Myself|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return MyselfRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return Myself|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return Myself
	 */
	public function fetch() {
		return parent::fetch();
	}
} 