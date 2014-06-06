<?php
/**
 * User: Martin Chudoba
 * Date: 10.3.14
 * Time: 19:07
 */

namespace Model\Base;


use slimORM\Entity\Entity;
use slimORM\EntityManager;

abstract class BaseModel
{

	/** @var \slimORM\EntityManager */
	protected $entityManager;

	/** @var string */
	protected $entity;

	/** Constructor
	 * @param EntityManager $entityManager
	 * @param $entity
	 */
	public function __construct(EntityManager $entityManager, $entity)
	{
		$this->entityManager = $entityManager;
		$this->entity = $entity;
	}

	/**
	 * @param string $key
	 * @return Entity|NULL
	 */
	public function get($key)
	{
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return \slimORM\BaseRepository
	 */
	public function read(Paginator $paginator = NULL)
	{
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->read($paginator);
	}

	/**
	 * @return Entity|TRUE
	 */
	public function save()
	{
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->save();
	}

	/**
	 * @param Entity $entity
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $entity)
	{
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
	 * @param $condition
	 * @param array $parameters
	 * @return \slimORM\BaseRepository
	 */
	public function where($condition, $parameters = array()) {
		$repository = $this->entityManager->getRepository($this->entity);
		return $repository->where($condition, $parameters);
	}

	/**
	 * @return int|null
	 */
	public function getLastInsertID() {
		return $this->entityManager->getRepository($this->entity)->getLastInsertID();
	}
} 