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
use slimORM\EntityManager;

class MyselfRepository extends BaseModel {

	const ENTITY = '\Model\Myself\Entity\Myself';

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
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
	 * @param Myself $entity
	 * @return LibraryRepository
	 */
	public function push(Myself $entity) {
		return parent::push($entity);
	}
} 