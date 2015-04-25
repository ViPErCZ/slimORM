<?php
/**
 * User: Martin Chudoba
 * Date: 10.3.14
 * Time: 19:07
 */

namespace slimORM;


use Nette\Utils\Paginator;
use slimORM\Entity\Entity;

/**
 * Class BaseModel
 * @package Model\Base
 */
abstract class AbstractRepository {

	/** @var EntityManager */
	protected $entityManager;

	/** @var string */
	protected $entity;

	/**
	 * @param EntityManager $entityManager
	 * @param $entity
	 */
	public function __construct(EntityManager $entityManager, $entity) {
		$this->entityManager = $entityManager;
		$this->entity = $entity;
	}

	/**
	 * @param string $key
	 * @return Entity|NULL
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return \slimORM\BaseRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->read($paginator);
	}

	/**
	 * @return Entity|TRUE
	 */
	public function save() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->save();
	}

	/**
	 * @param Entity $entity
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $entity) {
		$repository = $this->entityManager->getRepository($this->entity);
		$repository->push($entity);
		return $repository;
	}

	/**
	 * @return Entity[]|NULL
	 */
	public function fetchAll() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetchAll();
	}

	/**
	 * @return Entity
	 */
	public function fetch() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetch();
	}

	/**
	 * @param string $key
	 * @param null $value
	 * @return array
	 * @throws Exceptions\RepositoryException
	 */
	public function fetchPairs($key, $value = NULL) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetchPairs($key, $value);
	}

	/**
	 * @param $condition
	 * @param array $parameters
	 * @return \slimORM\BaseRepository
	 */
	public function where($condition, $parameters = array()) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->where($condition, $parameters);
	}

	/**
	 * @param mixed $key
	 * @return $this
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	public function wherePrimary($key) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->wherePrimary($key);
	}

	/**
	 * @param null|string $column
	 * @return int
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	public function count($column = null) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->count($column);
	}

	/**
	 * @return int|null
	 */
	public function getLastInsertID() {
		return $this->entityManager->getRepository($this->entity)->getLastInsertID();
	}

	/**
	 *
	 */
	public function clear() {
		$this->entityManager->getRepository($this->entity)->clear();
	}

	/**
	 * @param int $limit
	 * @param null $offset
	 * @return \slimORM\BaseRepository
	 */
	public function limit($limit, $offset = NULL) {
		return $this->entityManager->getRepository($this->entity)->limit($limit, $offset);
	}
} 