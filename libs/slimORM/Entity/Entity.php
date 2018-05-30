<?php

namespace slimORM\Entity;

use Nette\Database\Table\ActiveRow;
use Nette\Reflection\ClassType;
use Nette\Utils\ObjectHelpers;
use slimORM\Entity\Exception\EntityException;
use slimORM\EntityManager;
use slimORM\Reflexion\EntityReflexion;

/**
 * Class Entity
 * @package slimORM\Entity
 */
abstract class Entity {

	/** @var ActiveRow */
	protected $row;

	/** @var array */
	protected $references;

	/** @var EntityManager */
	protected $entityManager;

	/**
	 * Entity constructor.
	 * @param ActiveRow|null $row
	 */
	public function __construct(ActiveRow $row = NULL) {
		$this->references = array();
		$this->row = $row;
		if ($this->row) {
			$this->evaluated();
		}
	}

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager(EntityManager $entityManager): void {
		$this->entityManager = $entityManager;
	}


	/**
	 * @return ActiveRow|null
	 */
	final public function toRow(): ?ActiveRow {
		return $this->row;
	}

	/**
	 * @return array
	 * @throws EntityException
	 */
    public function getReferences(): array {
		if (\count($this->references) === 0) {
			$this->references = EntityReflexion::getReferences(\get_class($this));
		}
		return $this->references;
	}

	/**
	 * @return array
	 * @throws EntityException
	 */
	public function getLoadedReferences(): array {
		$this->getReferences();
		$loadedReferences = array();

		foreach ($this->references as $key => $reference) {
			if ($this->$key) {
				$loadedReferences[$key] = $reference;
			}
		}

		return $loadedReferences;
	}

	/**
	 * @param $propertyName
	 * @param $table
	 * @param $relatedKey
	 * @param $entityClass
	 * @return array|callable|mixed|null|\slimORM\BaseRepository|instance
	 * @throws EntityException
	 * @throws \ErrorException
	 * @throws \Throwable
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	final protected function oneToMany($propertyName, $table, $relatedKey, $entityClass) {
		if ($this->$propertyName === NULL && $this->row) {
			$items = $this->row->related($table, $relatedKey);
		
			if ($items->count('*') > 0) {
				if ($this->entityManager) {
					return $this->entityManager->getRepository($entityClass)->read()->where($relatedKey, $this->row->getPrimary());
				} else {
					throw new EntityException('Please set EntityManager instance to ' . \get_class($this) . ' class.');
				}
			} else {
				$this->$propertyName = null; //array();
			}
		} else if ($this->$propertyName === NULL && $this->row === NULL) {
			$this->$propertyName = null; //array();
		}

		return $this->$propertyName;
	}

	/**
	 * @param $propertyName
	 * @param $table
	 * @param $referenceKey
	 * @param $entityClass
	 * @return array|callable|null|instance
	 */
	final protected function oneToOne($propertyName, $table, $referenceKey, $entityClass) {
		if ($this->$propertyName === NULL && $this->row) {
			$row = $this->row->ref($table, $referenceKey);
			if ($row) {
				$this->$propertyName = new $entityClass($row);
				$this->$propertyName->setEntityManager($this->entityManager);
			}
		}
		return $this->$propertyName;
	}

	/**
	 * @param $propertyName
	 * @param $table
	 * @param $relatedKey
	 * @param $entityClass
	 * @return array|callable|null|instance
	 */
	final protected function manyToOne($propertyName, $table, $relatedKey, $entityClass) {
		if ($this->row) {
			$items = $this->row->related($table, $relatedKey);
			if ($items->count() > 0) {
				$this->$propertyName = new $entityClass($items->fetch());
				$this->$propertyName->setEntityManager($this->entityManager);
			}
		}
		return $this->$propertyName;
	}

	/**
	 * Returns primary key value
	 * @return mixed
	 */
	final public function getPrimary() {
		return $this->row ? $this->row->getPrimary() : NULL;
	}

	/**
	 * @param array $values
	 */
    final public function setValues(array $values): void {
		if (\is_array($values)) {
			foreach (array_keys($this->toArray()) as $key) {
				if (array_key_exists($key, $values)) {
					$this->$key = $values[$key];
				}
			}
		}
	}

	/**
	 * @return array
	 */
	final public function toArray(): array {
		$arr = array();
		foreach ($this->getColumns() as $property) {
			$name = $property['name'];
			$getter = 'get' . ucfirst($name);
			$arr[$name] = $this->$getter();
		}
		return $arr;
	}

	/**
	 * @return array
	 */
	final public function getColumns(): array {
		return EntityReflexion::getColumns(\get_class($this));
	}
	
	/**
	 * 
	 */
	final protected function evaluated(): void {
		if ($this->row) {
			foreach ($this->row->toArray() as $key => $item) {
				foreach ($this->getColumns() as $column) {
					if ($column['name'] === $key) {
						$this->$key = $item;
						break;
					}
				}
			}
		}
	}

	/**
	 * @param $name
	 * @return array|callable|mixed|null|\slimORM\BaseRepository|instance
	 * @throws EntityException
	 * @throws \ErrorException
	 * @throws \Throwable
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
    public function &__get($name) {
		$getter = 'get' .ucfirst($name);
		$reflection = ClassType::from(\get_class($this));
		if ($reflection->hasMethod($getter)) {
			$reflection->getMethod($getter);
			$val = $this->$getter();
			return $val;
		} else if ($reflection->hasProperty($name)) {
			$references = $this->getReferences();
			if ($this->$name === null && array_key_exists($name, $references) === true && $this->row) {
				$val = null;
				switch($references[$name]->linkage) {
					case 'OneToMany':
						if ($this->row->getTable()->getPrimary(TRUE) === $references[$name]->key) {
							$val = $this->oneToMany($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						} else {
							$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						}
						break;
					case 'OneToOne':
						if ($this->row->getTable()->getPrimary(TRUE) === $references[$name]->key) {
							$val = $this->manyToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						} else {
							$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						}
						break;
					case 'ManyToOne':
						if ($this->row->getTable()->getPrimary(true) === $references[$name]->key) {
							$val = $this->manyToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						} else {
							$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						}
						break;
					case 'ManyToMany':
						if ($this->row->getTable()->getPrimary(true) === $references[$name]->key) {
							$val = $this->oneToMany($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						} else {
							$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
						}
						break;
				}
				$this->$name = $val;
			}
		}
		return $this->$name;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		$setter = 'set' . ucfirst($name);
		$reflection = ClassType::from(\get_class($this));
		if ($reflection->hasMethod($setter)) {
			$this->$setter($value);
		} else if ($reflection->hasProperty($name)) {
			$this->$name = $value;
		} else {
			ObjectHelpers::strictSet(\get_class($this), $name);
		}
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->$name);
	}

	/**
	 * @throws EntityException
	 * @throws \ErrorException
	 * @throws \Throwable
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	final public function referencePrepare(): void {
		if ($this->toRow() === null) {
			$references = $this->getReferences();

			foreach ($references as $reference) {
				$class = $reference->targetEntity;
				$property = $reference->property;
				$entities = $this->$property;

				if ($reference->linkage === 'OneToMany' && \is_array($entities)) {
					$repository = clone $this->entityManager->getRepository($class);
					$repository->clear();

					foreach ($entities as $entity) {
						$repository->push($entity);
					}
					$this->$property = $repository;
				}
			}
		}
	}

}
