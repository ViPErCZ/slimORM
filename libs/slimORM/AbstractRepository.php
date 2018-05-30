<?php

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
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->get($key);
	}

	/**
	 * @param Paginator|null $paginator
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function read(Paginator $paginator = null) {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->read($paginator);
	}

	/**
	 * @param $columns
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
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
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	protected function containSelectPrimaryKey($columns): bool {
		return strpos($columns, $this->entityManager->getRepository($this->entity)->getPrimaryKey()) !== false;
	}

	/**
	 * @return bool
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function save() {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->save();
	}

	/**
	 * @param Entity $entity
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function push(Entity $entity) {
		$repository = $this->entityManager->getRepository($this->entity);
		$repository->push($entity);

		return $repository;
	}

	/**
	 * @return array
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function fetchAll() {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->fetchAll();
	}

	/**
	 * @return array|mixed
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function fetch() {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->fetch();
	}

	/**
	 * @param $key
	 * @param null $value
	 * @return array
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function fetchPairs($key, $value = null) {
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
	 * @throws \Throwable
	 */
	public function where($condition, $parameters) {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->where($condition, $parameters);
	}

	/**
	 * @param $key
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function wherePrimary($key) {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->wherePrimary($key);
	}

	/**
	 * @param null $column
	 * @return int
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function count($column = null) {
		$repository = $this->entityManager->getRepository($this->entity);

		return $repository->count($column);
	}

	/**
	 * @return int|null
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function getLastInsertID() {
		return $this->entityManager->getRepository($this->entity)->getLastInsertID();
	}

	/**
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function clear() {
		$this->entityManager->getRepository($this->entity)->clear();
	}

	/**
	 * @param $limit
	 * @param null $offset
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function limit($limit, $offset = null) {
		return $this->entityManager->getRepository($this->entity)->limit($limit, $offset);
	}

	/**
	 * @param $columns
	 * @return BaseRepository
	 * @throws Entity\Exception\EntityException
	 * @throws Exceptions\RepositoryException
	 * @throws \ErrorException
	 * @throws \Throwable
	 */
	public function order($columns) {
		return $this->entityManager->getRepository($this->entity)->order($columns);
	}
} 