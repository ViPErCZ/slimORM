<?php

namespace slimORM;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Reflection\ClassType;
use Nette\Utils\Paginator;
use phpDocumentor\Reflection\Types\Iterable_;
use slimORM\Entity\Entity;
use slimORM\Exceptions\RepositoryException;
use slimORM\Reflexion\EntityReflexion;
use stdClass;

/**
 * Description of BaseRepository
 *
 * @author Martin Chudoba
 */
abstract class BaseRepository implements \IteratorAggregate, \Countable {

	/** @var Context */
	protected $database;

	/** @var string */
	protected $entity;

	/** @var string */
	protected $table;

	/** @var array */
	protected $rows;

	/** @var Selection */
	protected $selection;

	/** @var array of primary key values */
	protected $keys = array();

	/** @var array */
	protected $protectLoop = array();

	/** @var array */
	protected $originalLoop = array();

	/**
	 * @param Context $connection
	 * @param string $tableName
	 * @param string $entityClass
	 * @throws RepositoryException
	 */
	public function __construct(Context $connection, $tableName, $entityClass) {
		$this->database = $connection;
		$this->table = (string) $tableName;
		$this->entity = (string) $entityClass;
		$this->rows = array();

		if (!isset($this->entityManager)) {
			throw new RepositoryException('Direct extends ' . __CLASS__ . '. Please use abstract class slimORM\AbstractRepository');
		}
	}

	/**
	 * @param Paginator|null $paginator
	 * @return Selection
	 */
	protected function buildSql(Paginator $paginator = null): Selection {
		$result = $this->database->table($this->table);
		if ($paginator !== null) {
			$result->limit($paginator->itemsPerPage, $paginator->offset);
		}

		return $result;
	}

	/**
	 * @return array|null|string
	 */
	public function getPrimaryKey() {
		$key = $this->database->table($this->table)->getPrimary();
		if (\is_array($key)) {
			return array_values($key)[0];
		} else {
			return $key;
		}
	}

	/**
	 * @param ActiveRow $row
	 * @param bool $refresh
	 * @return mixed
	 * @throws RepositoryException
	 */
	protected function createEntity(ActiveRow $row, $refresh = false) {
		$primary = $row->getPrimary();
		if (\is_int($primary)) {
			if ($refresh) {
				$this->rows[$primary] = new $this->entity($this->buildSql()->get($primary));
				$this->rows[$primary]->setEntityManager($this->entityManager);
			} else {
				$this->rows[$primary] = new $this->entity($row);
				$this->rows[$primary]->setEntityManager($this->entityManager);
			}
		} else {
			throw new RepositoryException('Table "' . self::TABLE . '" does not have a primary key.');
		}

		return $this->rows[$primary];
	}

	/**
	 * @return int|null
	 * @throws RepositoryException
	 */
	public function getLastInsertID() {
		if (\count($this->rows) > 0) {
			$end = end($this->rows);
			if ($end->toRow() && $end->toRow()->getPrimary()) {
				return (int) $end->toRow()->getPrimary();
			} else if ($end->toRow() === null) {
				return null;
			} else {
				throw new RepositoryException('Table "' . self::TABLE . '" does not have a primary key.');
			}
		} else {
			return null;
		}
	}

	/**
	 * @param $key
	 * @return mixed|null
	 * @throws RepositoryException
	 */
	public function get($key) {
		if (isset($this->rows[(int) $key])) {
			return $this->rows[(int) $key];
		} else {
			/** @var ActiveRow|false $item */
			$item = $this->buildSql()->get((int) $key);
			if ($item) {
				return $this->createEntity($item);
			} else {
				return null;
			}
		}
	}

	/**
	 * @param $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function select($columns): BaseRepository {
		if ($this->selection) {
			if ($this->containSelectPrimaryKey($columns) === false) {
				$columns .= ',' . $this->getPrimaryKey();
			}
			$this->selection->select($columns);
		} else {
			throw new RepositoryException('Before using the function select(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $columns
	 * @return bool
	 */
	protected function containSelectPrimaryKey($columns): bool {
		return strpos($columns, $this->getPrimaryKey()) !== false;
	}

	/**
	 * @param $limit
	 * @param null $offset
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function limit($limit, $offset = null): BaseRepository {
		if ($this->selection) {
			$this->selection->limit($limit, $offset);
		} else {
			throw new RepositoryException('Before using the function limit(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $condition
	 * @param array|int|string $parameters
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function where($condition, ...$parameters): BaseRepository {
		if ($this->selection) {
			$this->selection->where($condition, ...$parameters);
		} else {
			throw new RepositoryException('Before using the function where(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $key
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function wherePrimary($key): BaseRepository {
		if ($this->selection) {
			$this->selection->wherePrimary($key);
		} else {
			throw new RepositoryException('Before using the function wherePrimary(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function order($columns): BaseRepository {
		if ($this->selection) {
			$this->selection->order($columns);
		} else {
			throw new RepositoryException('Before using the function order(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function group($columns): BaseRepository {
		if ($this->selection) {
			$this->selection->group($columns);
		} else {
			throw new RepositoryException('Before using the function group(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param string $having
	 * @return BaseRepository
	 * @throws Exceptions\RepositoryException
	 */
	public function having($having): BaseRepository {
		if ($this->selection) {
			$this->selection->having($having);
		} else {
			throw new RepositoryException('Before using the function group(...) call read(...).');
		}
		return $this;
	}

	/**
	 * @param $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function min($column): int {
		if ($this->selection) {
			return $this->selection->min($column);
		} else {
			throw new RepositoryException('Before using the function min(...) call read(...).');
		}
	}

	/**
	 * @param $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function max($column): int {
		if ($this->selection) {
			return $this->selection->max($column);
		} else {
			throw new RepositoryException('Before using the function max(...) call read(...).');
		}
	}

	/**
	 * @param $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function sum($column): int {
		if ($this->selection) {
			return $this->selection->sum($column);
		} else {
			throw new RepositoryException('Before using the function sum(...) call read(...).');
		}
	}

	/**
	 * @param $key
	 * @param null $value
	 * @return array
	 */
	public function fetchPairs($key, $value = null): array {
		$return = array();
		foreach ($this as $row) {
			$return[$row->$key] = $value ? $row->$value : $row->$key;
		}
		return $return;
	}

	/**
	 * @return array
	 * @throws RepositoryException
	 */
	public function fetchAll(): array {
		if ($this->selection) {
			/** @var Iterable_ $entity */
			foreach ($this->selection as $entity) {
				$this->createEntity($entity);
			}
		}

		return $this->rows;
	}

	/**
	 * @return array|mixed
	 * @throws RepositoryException
	 */
	public function fetch() {
		if ($this->selection) {
			$fetch = $this->selection->fetch();
			if ($fetch) {
				return $this->createEntity($fetch);
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	/**
	 * @return bool
	 */
	protected function hasNonSaveEntity(): bool {
		foreach($this->rows as $row) {
			if ($row->toRow() === NULL) {
				return true;
			} else {
				$reflection = $row->getColumns();
				if (\is_array($reflection)) {
					foreach ($reflection as $property) {
						$name = $property['name'];
						if ($row->toRow()->$name !== $row->$name) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 *
	 */
	public function clear() {
		$this->rows = array();
	}

	/**
	 * @param Paginator|null $paginator
	 * @return BaseRepository
	 */
	public function read(Paginator $paginator = NULL): BaseRepository {
		if ($this->hasNonSaveEntity() === FALSE) {
			$this->rows = array();
		}
		$this->selection = $this->buildSql($paginator);
		return $this;
	}

	/**
	 * @param Entity $entity
	 */
	public function push(Entity $entity) {
		$entity->setEntityManager($this->entityManager);
		if ($entity->toRow()) {
			$this->rows[$entity->getPrimary()] = $entity;
		} else {
			$this->rows[] = $entity;
		}
	}

	/**
	 * @return Selection
	 */
	public function getSelection(): Selection {
		if ($this->selection === null) {
			$this->selection = $this->buildSql();
		}
		return $this->selection;
	}

	/**
	 * @param null $key
	 * @throws \PDOException
	 * @throws RepositoryException
	 */
	public function delete($key = null) {
		$ownerTransaction = false;
		try {
			if ($this->database->getConnection()->getPdo()->inTransaction() === false) {
				$this->database->beginTransaction();
				$ownerTransaction = true;
			}
			if (\is_array($key)) {
				$this->buildSql()->wherePrimary($key)->delete();
			} else if (\is_int($key)) {
				$row = $this->get($key);
				if ($row) {
					$row->toRow()->delete();
					unset($this->rows[$key]);
				} else {
					throw new \PDOException('Item with primary key ' . $key . ' not found.');
				}
			} else if ($key === null) {
				if ($this->selection) {
					$this->selection->delete();
					$this->rows = array();
				}
			}
			if ($ownerTransaction && $this->database->getConnection()->getPdo()->inTransaction()) {
				$this->database->commit();
			}
		} catch (\PDOException $e) {
			if ($ownerTransaction && $this->database->getConnection()->getPdo()->inTransaction()) {
				$this->database->rollBack();
			}
			throw new \PDOException($e->getMessage());
		}
	}

	/**
	 * @return bool
	 * @throws \ErrorException
	 * @throws \Nette\InvalidArgumentException
	 * @throws \PDOException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 */
	public function save(): bool {
		$ownerTransaction = FALSE;
		try {
			if ($this->database->getConnection()->getPdo()->inTransaction() === FALSE) {
				$this->database->beginTransaction();
				$ownerTransaction = TRUE;
			}
			if (\is_array($this->rows)) {
				foreach ($this->rows as $key => $row) {
					if ($row->toRow()) {
						$this->update($row);
					} else {
						$row->referencePrepare();
						$returnEntity = $this->insert($row);
						unset($this->rows[$key]);
						$this->rows[$returnEntity->getPrimary()] = $returnEntity;
					}
					$this->protectLoop = $this->originalLoop;
				}
			}
			if ($ownerTransaction && $this->database->getConnection()->getPdo()->inTransaction()) {
				$this->database->commit();
			}
			return TRUE;
		} catch (\PDOException $e) {
			if ($ownerTransaction && $this->database->getConnection()->getPdo()->inTransaction()) {
				$this->database->rollBack();
			}
			//var_dump($e->getTrace());
			throw new \PDOException($e->getMessage());
		}
	}

	/**
	 * @param Entity $entity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 */
	protected function update(Entity $entity) {
		$this->updateActiveRow($entity);
		$this->referencesUpdate($entity, $entity->getLoadedReferences());
	}

	/**
	 * @param Entity $entity
	 */
	private function updateActiveRow(Entity $entity) {
		$reflection = $entity->getColumns();
		$record = array();
		foreach ($reflection as $property) {
			$name = $property['name'];
			if ($entity->toRow()->$name !== $entity->$name) {
				$record[$name] = $entity->$name;
			}
		}
		if (\count($record) > 0) {
			$entity->toRow()->update($record);
		}
		$this->addLoop($entity);
	}

	/**
	 * @param Entity $entity
	 * @param array $references
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws RepositoryException
	 * @throws Entity\Exception\EntityException
	 */
	private function referencesUpdate(Entity $entity, array $references) {
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$getter = 'get' . ucfirst($name);
			/** @var BaseRepository $referenceEntity */
			$referenceEntity = $entity->$getter();
			if ($referenceEntity instanceof self) {
				$setter = 'set' . ucfirst($mappedBy);
				$getterKey = 'get' . ucfirst($mappedBy);
				if ($entity->$getter()->isEmpty() === false) {
					$index = 0;
					/** @var Entity $item */
					foreach ($referenceEntity as $item) {
						if ($index === 0) {
							$this->addLoop($item);
						}
						if ($item->toRow() === null) {
							$item->$setter($entity->$getterKey());
						}
						$index++;
					}
					$entity->$getter()->setLoop($this->protectLoop);
					$entity->$getter()->save();
				}
			} else {
				/** @var Entity|null $refEntity */
				$refEntity = $entity->$getter();
				if ($refEntity) {
					if ($this->checkLoop($refEntity)) {
						continue;
					}
					if ($refEntity->toRow()) {
						$this->updateActiveRow($refEntity);
						$this->referencesUpdate($refEntity, $refEntity->getReferences());
					} else {
						$getterKey = 'get' . ucfirst($mappedBy);
						$refEntity->$mappedBy = $entity->$getterKey();
						$table = EntityReflexion::getTable($reference->targetEntity);
						$row = $this->database->table($table)->insert($refEntity->toArray());
						$this->addLoop($refEntity);
						$this->referencesInsert($refEntity, $row->getPrimary(), $refEntity->getReferences());
					}
				}
			}
		}
	}

	/**
	 * @param Entity $entity
	 * @return mixed
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 */
	protected function insert(Entity $entity) {
		$this->beforeCheckedReferences($entity);
		/** @var ActiveRow|bool|int $row */
		$row = $this->buildSql()->insert($entity->toArray());
		if (\is_bool($row) || \is_int($row)) {
			throw new RepositoryException('Table "' . EntityReflexion::getTable($entity) . '" has no primary key.');
		} else {
			$newEntity = $this->createEntity($row, TRUE);
			$primaryKey = $row->getPrimary();
			$primaryName = $this->database->table(EntityReflexion::getTable($entity))->getPrimary();
			$entity->$primaryName = $primaryKey;
			$this->addLoop($entity);
			$references = $entity->getReferences();
			$this->referencesInsert($entity, $primaryKey, $references);
			return $newEntity;
		}
	}

	/**
	 * @param Entity $entity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 */
	private function beforeCheckedReferences(Entity &$entity) {
		$references = $entity->getReferences();
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$primaryName = $this->database->table(EntityReflexion::getTable($entity))->getPrimary();

			if ($entity->$name && $entity->$name instanceof Entity && $reference->linkage === 'OneToOne' && $primaryName !== $mappedBy) {
				/** @var Entity $refEntity */
				$refEntity = $entity->$name;
				$table = EntityReflexion::getTable($reference->targetEntity);
				if ($reference->canBeNULL === false && $mappedBy === $this->database->table($table)->getPrimary()
					&& \get_class($entity) === \get_class($refEntity)) {
					throw new RepositoryException('Property ' . $reference->targetEntity . ':' . $mappedBy . ' cannot be NULL.');
				} else {
					if ($entity->$mappedBy !== NULL) { //mapped property set directly
						continue;
					} else if ($refEntity->toRow() === NULL) {
						$entity->$mappedBy = $this->recRevertInsert($refEntity, $reference, $name, $entity);
						$entity->$name = NULL;
					}
				}
			}
		}
	}

	/**
	 * @param Entity $entity
	 * @param $primaryKey
	 * @param array $references
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws \Nette\InvalidArgumentException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 * @throws \ErrorException
	 */
	private function referencesInsert(Entity $entity, $primaryKey, array $references) {
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$getter = 'get' . ucfirst($name);
			if ($reference->linkage === 'OneToMany') {
				$entity->referencePrepare();
			}
			if ($entity->$getter() instanceof self) {
				$setter = 'set' . ucfirst($mappedBy);
				//$getterKey = $entity->getPrimary();
				$table = EntityReflexion::getTable(\get_class($entity));
				$primary = $this->database->table($table)->getPrimary();
				if ($primary !== $mappedBy) {
					$primaryKeyValue = $primaryKey;
				} else {
					$method = 'get' . ucfirst($mappedBy);
					$primaryKeyValue = $entity->$method();
				}

				$index = 0;
				/** @var BaseRepository $repository */
				$repository = $entity->$name;
				/** @var Entity $item */
				foreach ($repository as $item) {
					if ($index === 0) {
						$this->addLoop($item);
					}
					if ($item->toRow() === NULL) {
						$item->$setter($primaryKeyValue);
					}
					$index++;
				}
				$entity->$getter()->setLoop($this->protectLoop);
				$entity->$getter()->save();
			} else {
				/** @var Entity|null $refEntity */
				$refEntity = $entity->$name;
				if ($refEntity) {
					if ($this->checkLoop($refEntity)) {
						continue;
					}
					$getterKey = 'get' . ucfirst($mappedBy);
					$refEntity->$mappedBy = $entity->$getterKey();
					$refEntityReferences = $refEntity->getReferences();

					/** Kontrola jestli není potřeba referenci uložit dříve než samotnou entitu */
					/** @var Entity $refEntity */
					$refEntity = $this->beforeInsert($entity, $refEntityReferences, $name);

					$table = EntityReflexion::getTable($reference->targetEntity);
					$row = $this->database->table($table)->insert($refEntity->toArray());
					$primaryKey = $row->getPrimary();
					$primaryName = $this->database->table(EntityReflexion::getTable($refEntity))->getPrimary();
					$refEntity->$primaryName = $primaryKey;
					$this->addLoop($refEntity);
					$this->referencesInsert($refEntity, $row->getPrimary(), $refEntityReferences, $entity);
				}
			}
		}
	}

	/**
	 * @param Entity $entity
	 * @param array $references
	 * @param $name
	 * @param Entity|null $current
	 * @return Entity
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws \Nette\InvalidArgumentException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 * @throws \ErrorException
	 */
	private function beforeInsert(Entity $entity, &$references, $name, Entity $current = null): Entity {
		/** @var Entity $refEntity */
		$refEntity = $entity->$name;
		/** Kontrola jestli není potřeba referenci uložit dříve než samotnou entitu */
		foreach ($references as $key => $ref) {
			$reflection = ClassType::from($ref->targetEntity);
			if ($ref->linkage === 'OneToOne' && $reflection->getName() !== \get_class($entity) && $reflection->getName() !== \get_class($current)) {
				$refMappedBy = $ref->key;
				$refName = $ref->property;
				if ($refEntity->$refMappedBy !== NULL) { //mapped property set directly
					continue;
				} else if ($refEntity->$refName) {
					if ($refEntity->$refName->$refMappedBy || $refEntity->$refName->toRow()) {
						$this->update($refEntity->$refName);
					} else {
						$refEntity->$refMappedBy = $this->recRevertInsert($refEntity->$refName, $ref, $refName, $refEntity);
						$refEntity->$refName->$refMappedBy = $refEntity->$refMappedBy;
					}
				} else if ($ref->canBeNULL === false) {
					throw new RepositoryException('Property ' . $ref->targetEntity . ':' . $refMappedBy . ' cannot be NULL.');
				}
			}
		}
		return $refEntity;
	}

	/**
	 * @param Entity $entity
	 * @param $reference
	 * @param $name
	 * @param Entity $parent
	 * @return mixed
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws Entity\Exception\EntityException
	 * @throws RepositoryException
	 */
	private function recRevertInsert(Entity $entity, $reference, $name, Entity $parent) {
		$refReferences = $entity->getReferences();
		$entity = $this->beforeInsert($parent, $refReferences, $name, $entity);
		$table = EntityReflexion::getTable($reference->targetEntity);
		$row = $this->database->table($table)->insert($entity->toArray());
		$this->addLoop($entity);
		$primaryKey = $row->getPrimary();
		$primaryName = $this->database->table(EntityReflexion::getTable($entity))->getPrimary();
		$entity->$primaryName = $primaryKey;
		$this->referencesInsert($entity, $row->getPrimary(), $refReferences, $entity);
		return $row->getPrimary();
	}

	/** Check loop protection
	 * @param Entity $entity
	 * @return bool
	 */
	private function checkLoop(Entity $entity): bool {
		foreach($this->protectLoop as $loop) {
			if (EntityReflexion::getTable($loop) === EntityReflexion::getTable($entity) && $entity->getPrimary() === $loop->getPrimary()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param Entity $entity
	 */
	private function addLoop(Entity $entity) {
		if ($this->checkLoop($entity) === false) {
			$this->protectLoop[] = $entity;
		}
	}

	/**
	 * @param array $loop
	 */
	public function setLoop(array $loop) {
		$this->originalLoop = $loop;
		$this->protectLoop = $loop;
	}

	/**
	 * @return bool
	 */
	public function isEmpty(): bool {
		return \count($this->rows) <= 0;
	}


	/*	 * ******************* interface \IteratorAggregate *****************  */

	public function getIterator() {
		if ($this->selection && \count($this->rows) === 0) {
			$this->fetchAll();
		}
		return \count($this->rows) > 0 ? new \ArrayIterator($this->rows) : new \ArrayIterator();
	}

	/*	 * ******************* interface \Countable *****************  */

	/** Count
	 * 
	 * @param string $column
	 * @return int
	 */
	public function count($column = NULL): int {
		if ($this->selection === null) {
			$this->read();
		}
		return $this->selection->count($column);
	}

	/*	 * ******************* interface Iterator *****************	 */

	public function rewind() {
		$this->keys = array_keys($this->rows);
		reset($this->keys);
	}

	public function current() {
		return $this->rows[current($this->keys)];
	}

	public function key() {
		return current($this->keys);
	}

	public function next() {
		next($this->keys);
	}

	public function valid() {
		return current($this->keys) !== FALSE;
	}

}
