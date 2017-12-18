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
 * Class AbstractRepository
 * @package slimORM
 */
abstract class AbstractRepository {

	/** @var EntityManager */
	protected $entityManager;

	/** @var string */
	protected $entity;

	/**
	 * AbstractRepository constructor.
	 * @param EntityManager $entityManager
	 * @param $entity
	 */
	public function __construct(EntityManager $entityManager, $entity) {
		$this->entityManager = $entityManager;
		$this->entity = $entity;
	}

	/**
	 * @param $key
	 * @return mixed|null
	 * @throws \Nette\InvalidArgumentException
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->get($key);
	}

	/**
	 * @param Paginator|NULL $paginator
	 * @return BaseRepository
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->read($paginator);
	}

	/**
	 * @param string $columns
	 * @return BaseRepository
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function select($columns) {
		$repository = $this->entityManager->getRepository($this->entity);
		if ($this->containSelectPrimaryKey($columns) === false) {
			$columns .= ',' . $repository->getPrimaryKey();
		}
		return $repository->select($columns);
	}

	/**
	 * @param $columns
	 * @return bool
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	protected function containSelectPrimaryKey($columns): bool {
		return strpos($columns, $this->entityManager->getRepository($this->entity)->getPrimaryKey()) !== false;
	}

	/**
	 * @return bool
	 * @throws \PDOException
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function save() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->save();
	}

	/**
	 * @param Entity $entity
	 * @return BaseRepository
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function push(Entity $entity) {
		$repository = $this->entityManager->getRepository($this->entity);
		$repository->push($entity);
		return $repository;
	}

	/**
	 * @return Entity[]|NULL
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function fetchAll() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetchAll();
	}

	/**
	 * @return array|Entity
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function fetch() {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetch();
	}

	/**
	 * @param string $key
	 * @param null|string $value
	 * @return array
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function fetchPairs($key, $value = NULL) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->fetchPairs($key, $value);
	}

	/**
	 * @param $condition
	 * @param $parameters
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 */
	public function where($condition, $parameters): BaseRepository {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->where($condition, $parameters);
	}

	/**
	 * @param $key
	 * @return $this
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function wherePrimary($key) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->wherePrimary($key);
	}

	/**
	 * @param null|string $column
	 * @return int
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function count($column = null) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->count($column);
	}

	/**
	 * @return int|null
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function getLastInsertID() {
		return $this->entityManager->getRepository($this->entity)->getLastInsertID();
	}

	/**
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function clear() {
		$this->entityManager->getRepository($this->entity)->clear();
	}

	/**
	 * @param int $limit
	 * @param null $offset
	 * @return BaseRepository
	 */
	public function limit($limit, $offset = NULL) {
		return $this->entityManager->getRepository($this->entity)->limit($limit, $offset);
	}

	/**
	 * @param string $columns
	 * @return BaseRepository
	 * @throws \ErrorException
	 * @throws Exceptions\RepositoryException
	 */
	public function order($columns) {
		return $this->entityManager->getRepository($this->entity)->order($columns);
	}
} 