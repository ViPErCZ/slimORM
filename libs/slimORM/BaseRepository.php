<?php

namespace slimORM;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Diagnostics\Debugger;
use Nette\Reflection\ClassType;
use Nette\Utils\Paginator;
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

	/** Konstruktor
	 *
	 * @param \Nette\Database\Context $connection
	 * @param string $tableName
	 * @param string $entity
	 */
	public function __construct(Context $connection, $tableName, $entity) {
		$this->database = $connection;
		$this->table = (string) $tableName;
		$this->entity = (string) $entity;
		$this->selection = NULL;
		$this->rows = array();
	}

	/** Sestavení SQL dotazu
	 * 
	 * @param Paginator $paginator
	 * @return Selection
	 */
	protected function buildSql(Paginator $paginator = NULL) {
		$result = $this->database->table($this->table);
		if ($paginator !== NULL) {
			$result->limit($paginator->itemsPerPage, $paginator->offset);
		}

		return $result;
	}

	/** Vytvoří novou entitu
	 * 
	 * @param ActiveRow $row
	 * @param boolean $refresh
	 * @return Entity
	 * @throws RepositoryException
	 */
	protected function createEntity(ActiveRow $row, $refresh = FALSE) {
		$primary = $row->getPrimary();
		if (is_int($primary)) {
			if ($refresh) {
				$this->rows[$primary] = new $this->entity($this->buildSql()->get($primary));
				$this->rows[$primary]->setEntityManager($this->entityManager);
			} else {
				$this->rows[$primary] = new $this->entity($row);
				$this->rows[$primary]->setEntityManager($this->entityManager);
			}
		} else {
			throw new RepositoryException("Table \"" . self::TABLE . "\" does not have a primary key.");
		}

		return $this->rows[$primary];
	}

	/** Return last insert ID
	 * 
	 * @return int|null
	 * @throws RepositoryException
	 */
	public function getLastInsertID() {
		if (count($this->rows) > 0) {
			$end = end($this->rows);
			if ($end->toRow() && $end->toRow()->getPrimary()) {
				return (int) $end->toRow()->getPrimary();
			} else if ($end->toRow() === NULL) {
				return NULL;
			} else {
				throw new RepositoryException("Table \"" . self::TABLE . "\" does not have a primary key.");
			}
		} else {
			return NULL;
		}
	}

	/** Find item by primary key
	 * 
	 * @param int $key
	 * @return Entity|null
	 */
	public function get($key) {
		if (isset($this->rows[(int) $key])) {
			return $this->rows[(int) $key];
		} else {
			$item = $this->buildSql()->get((int) $key);
			if ($item) {
				return $this->createEntity($item);
			} else {
				return NULL;
			}
		}
	}

	/** Select
	 * 
	 * @param string $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function select($columns) {
		if ($this->selection)
			$this->selection->select($columns);
		else
			throw new RepositoryException("Before using the function select(...) call read(...).");
		return $this;
	}

	/**
	 * Sets limit clause, more calls rewrite old values.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function limit($limit, $offset = NULL) {
		if ($this->selection)
			$this->selection->limit($limit, $offset);
		else
			throw new RepositoryException("Before using the function limit(...) call read(...).");
		return $this;
	}

	/** Where
	 * 
	 * @param string $condition
	 * @param mixed $parameters
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function where($condition, $parameters = array()) {
		if ($this->selection)
			$this->selection->where($condition, $parameters);
		else
			throw new RepositoryException("Before using the function where(...) call read(...).");
		return $this;
	}

	/**
	 * @param mixed $key
	 * @return $this
	 * @throws RepositoryException
	 */
	public function wherePrimary($key) {
		if ($this->selection)
			$this->selection->wherePrimary($key);
		else
			throw new RepositoryException("Before using the function wherePrimary(...) call read(...).");
		return $this;
	}

	/** Order
	 * 
	 * @param string $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function order($columns) {
		if ($this->selection)
			$this->selection->order($columns);
		else
			throw new RepositoryException("Before using the function order(...) call read(...).");
		return $this;
	}

	/** Group
	 * 
	 * @param string $columns
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function group($columns) {
		if ($this->selection)
			$this->selection->group($columns);
		else
			throw new RepositoryException("Before using the function group(...) call read(...).");
		return $this;
	}

	/**
	 * @param string $having
	 * @return $this
	 * @throws Exceptions\RepositoryException
	 */
	public function having($having) {
		if ($this->selection)
			$this->selection->having($having);
		else
			throw new RepositoryException("Before using the function group(...) call read(...).");
		return $this;
	}

	/** Min
	 * 
	 * @param string $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function min($column) {
		if ($this->selection)
			return $this->selection->min($column);
		else
			throw new RepositoryException("Before using the function min(...) call read(...).");
	}

	/** Max
	 * 
	 * @param string $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function max($column) {
		if ($this->selection)
			return $this->selection->max($column);
		else
			throw new RepositoryException("Before using the function max(...) call read(...).");
	}

	/** Sum
	 * 
	 * @param string $column
	 * @return int
	 * @throws RepositoryException
	 */
	public function sum($column) {
		if ($this->selection)
			return $this->selection->sum($column);
		else
			throw new RepositoryException("Before using the function sum(...) call read(...).");
	}

	/**
	 * Returns all rows as associative array.
	 * @param  string
	 * @param  string column name used for an array value or NULL for the whole row
	 * @return array
	 */
	public function fetchPairs($key, $value = NULL) {
		$return = array();
		foreach ($this as $row) {
			$return[$row->$key] = $value ? $row->$value : $row->$key;
		}
		return $return;
	}

	/** Returns all rows
	 * 
	 * @return array|NULL
	 */
	public function fetchAll() {
		if ($this->selection) {
			foreach ($this->selection as $entity) {
				$this->createEntity($entity);
			}
		}

		return $this->rows;
	}

	/**
	 * Vrací výsledky po jednom
	 * @return array|Entity
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

	/** Zjisti, zda-li repozitar obsahuje nejake entity k ulozeni
	 * @return bool
	 */
	protected function hasNonSaveEntity() {
		foreach($this->rows as $row) {
			if ($row->toRow() === NULL) {
				return TRUE;
			} else {
				$reflection = $row->getColumns();
				$record = array();
				foreach ($reflection as $property) {
					$name = $property['name'];
					if ($row->toRow()->$name != $row->$name) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	/** Clear state */
	public function clear() {
		$this->rows = array();
	}

	/** Create new Selection
	 * 
	 * @param Paginator $paginator
	 * @return BaseRepository
	 */
	public function read(Paginator $paginator = NULL) {
		if ($this->hasNonSaveEntity() === FALSE) {
			$this->rows = array();
		}
		$this->selection = $this->buildSql($paginator);
		return $this;
	}

	/** Push entity to array
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
	 *
	 * @return NULL|Selection
	 */
	public function getSelection() {
		return $this->selection;
	}

	/** Delete
	 * @param null|int $key
	 * @throws \PDOException
	 */
	public function delete($key = NULL) {
		try {
			$ownerTransaction = FALSE;
			if ($this->database->getConnection()->getPdo()->inTransaction() === FALSE) {
				$this->database->beginTransaction();
				$ownerTransaction = TRUE;
			}
			if (is_array($key)) {
				$this->buildSql()->wherePrimary($key)->delete();
			} else if (is_int($key)) {
				$row = $this->get($key);
				if ($row) {
					$row->toRow()->delete();
					unset($this->rows[$key]);
				} else {
					throw new \PDOException("Item with primary key " . $key . " not found.");
				}
			} else if ($key === NULL) {
				if ($this->selection) {
					$this->selection->delete();
					$this->rows = array();
				}
			}
			if ($this->database->getConnection()->getPdo()->inTransaction() === TRUE && $ownerTransaction === TRUE) {
				$this->database->commit();
			}
		} catch (\PDOException $e) {
			if ($this->database->getConnection()->getPdo()->inTransaction() === TRUE && $ownerTransaction === TRUE) {
				$this->database->rollBack();
			}
			throw new \PDOException($e->getMessage());
		}
	}

	/** Save entity
	 * @return bool
	 * @throws \PDOException
	 */
	public function save() {
		try {
			$ownerTransaction = FALSE;
			if ($this->database->getConnection()->getPdo()->inTransaction() === FALSE) {
				$this->database->beginTransaction();
				$ownerTransaction = TRUE;
			}
			if (is_array($this->rows)) {
				foreach ($this->rows as $key => $row) {
					if ($row->toRow()) {
						$this->update($row);
					} else {
						$row->__referencePrepair();
						$returnEntity = $this->insert($row);
						unset($this->rows[$key]);
						$this->rows[$returnEntity->getPrimary()] = $returnEntity;
					}
					$this->protectLoop = $this->originalLoop;
				}
			}
			if ($this->database->getConnection()->getPdo()->inTransaction() === TRUE && $ownerTransaction === TRUE) {
				$this->database->commit();
			}
			return TRUE;
		} catch (\PDOException $e) {
			if ($this->database->getConnection()->getPdo()->inTransaction() === TRUE && $ownerTransaction === TRUE) {
				$this->database->rollBack();
			}
			//var_dump($e->getTrace());
			throw new \PDOException($e->getMessage());
		}
	}

	/** Update entity
	 * @param Entity $entity
	 */
	protected function update(Entity $entity) {
		$this->updateActiveRow($entity);
		$primaryKey = (int)$entity->toRow()->getPrimary(TRUE);
		$this->referencesUpdate($entity, $primaryKey, $entity->getLoadedReferences());
	}

	/** Update ActiveRow in current Entity
	 * @param Entity $entity
	 */
	private function updateActiveRow(Entity $entity) {
		$reflection = $entity->getColumns();
		$record = array();
		foreach ($reflection as $property) {
			$name = $property['name'];
			if ($entity->toRow()->$name != $entity->$name) {
				$record[$name] = $entity->$name;
			}
		}
		if (count($record) > 0) {
			$entity->toRow()->update($record);
		}
		$this->addLoop($entity);
	}

	/** Recursive references update
	 * @param Entity $entity
	 * @param int $primaryKey Primary key value
	 * @param array $references
	 * @param Entity $parent
	 * @throws \ReflectionException
	 */
	private function referencesUpdate(Entity $entity, $primaryKey, array $references, Entity $parent = NULL) {
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$getter = "get" . ucfirst($name);
			if ($entity->$getter() instanceof BaseRepository) {
				$setter = "set" . ucfirst($mappedBy);
				$getterKey = "get" . ucfirst($mappedBy);
				if ($entity->$getter()->isEmpty() === FALSE) {
					$iter = 0;
					foreach ($entity->$getter() as $item) {
						if ($iter === 0)$this->addLoop($item);
						if ($item->toRow() === NULL) {
							$item->$setter($entity->$getterKey());
						}
						$iter++;
					}
					$entity->$getter()->setLoop($this->protectLoop);
					$entity->$getter()->save();
				}
			} else {
				$refEntity = $entity->$getter();
				if ($refEntity) {
					if ($this->checkLoop($refEntity)) {
						continue;
					}
					if ($refEntity->toRow()) {
						$this->updateActiveRow($refEntity);
						$this->referencesUpdate($refEntity, $refEntity->toRow()->getPrimary(), $refEntity->getReferences(), $entity);
					} else {
						$getterKey = "get" . ucfirst($mappedBy);
						$refEntity->$mappedBy = $entity->$getterKey();
						$table = EntityReflexion::getTable($reference->targetEntity);
						$row = $this->database->table($table)->insert($refEntity->toArray());
						$this->addLoop($refEntity);
						$this->referencesInsert($refEntity, $row->getPrimary(), $refEntity->getReferences(), $entity);
					}
				}
			}
		}
	}

	/** Helper function
	 * @param string $name
	 * @param array $properties
	 * @return bool
	 */
	private function searchProperty($name, array $properties) {
		foreach ($properties as $property) {
			if ($name == $property['name'])
				return true;
		}
		return false;
	}

	/**
	 * Insert entity to database
	 * @param Entity $entity
	 * @return Entity
	 */
	protected function insert(Entity $entity) {
		$this->beforeCheckedReferences($entity);
		$row = $this->buildSql()->insert($entity->toArray());
		$newEntity = $this->createEntity($row, TRUE);
		$primaryKey = $row->getPrimary();
		$primaryName = $this->database->table(EntityReflexion::getTable($entity))->getPrimary();
		$entity->$primaryName = $primaryKey;
		$this->addLoop($entity);
		$references = $entity->getReferences();
		$this->referencesInsert($entity, $primaryKey, $references);
		return $newEntity;
	}

	/**
	 * @param Entity $entity
	 * @throws Exceptions\RepositoryException
	 */
	private function beforeCheckedReferences(Entity &$entity) {
		$references = $entity->getReferences();
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$primaryName = $this->database->table(EntityReflexion::getTable($entity))->getPrimary();

			if ($entity->$name instanceof Entity && $entity->$name !== NULL && $reference->linkage == "OneToOne" && $primaryName !== $mappedBy) {
				$refEntity = $entity->$name;
				$table = EntityReflexion::getTable($reference->targetEntity);
				if ($reference->canBeNULL === FALSE && $mappedBy == $this->database->table($table)->getPrimary()
					&& get_class($entity) == get_class($refEntity)) {
					throw new RepositoryException("Property " . $reference->targetEntity . ":" . $mappedBy . " cannot be NULL.");
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

	/** Recursive insert
	 * @param Entity $entity
	 * @param $primaryKey
	 * @param array $references
	 * @param Entity $parent
	 * @throws Exceptions\RepositoryException
	 * @throws \ReflectionException
	 */
	private function referencesInsert(Entity $entity, $primaryKey, array $references, Entity $parent = NULL) {
		foreach ($references as $reference) {
			$name = $reference->property;
			$mappedBy = $reference->key;
			$getter = "get" . ucfirst($name);
			if ($reference->linkage == "OneToMany") {
				$entity->__referencePrepair();
			}
			if ($entity->$getter() instanceof BaseRepository) {
				$setter = "set" . ucfirst($mappedBy);
				$getterKey = "get" . ucfirst($mappedBy);
				$iter = 0;
				foreach ($entity->$name as $item) {
					if ($iter === 0)$this->addLoop($item);
					if ($item->toRow() === NULL) {
						$item->$setter($entity->$getterKey());
					}
					$iter++;
				}
				$entity->$getter()->setLoop($this->protectLoop);
				$entity->$getter()->save();
			} else {
				$refEntity = $entity->$name;
				if ($refEntity) {
					if ($this->checkLoop($refEntity)) {
						continue;
					}
					$getterKey = "get" . ucfirst($mappedBy);
					$refEntity->$mappedBy = $entity->$getterKey();
					$refEntityReferences = $refEntity->getReferences();

					/** Kontrola jestli není potřeba referenci uložit dříve než samotnou entitu */
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

	/** Check before inserting
	 * @param Entity $entity
	 * @param array $references referenced array
	 * @param string $name
	 * @return Entity
	 * @throws Exceptions\RepositoryException
	 */
	private function beforeInsert(Entity $entity, &$references, $name) {
		$refEntity = $entity->$name;
		/** Kontrola jestli není potřeba referenci uložit dříve než samotnou entitu */
		foreach ($references as $key => $ref) {
			if ($ref->linkage == "OneToOne" && $ref->targetEntity != get_class($entity)) {
				$refMappedBy = $ref->key;
				$refName = $ref->property;
				if ($refEntity->$refMappedBy !== NULL) { //mapped property set directly
					continue;
				} else if ($refEntity->$refName) {
					if ($refEntity->$refName->toRow() || $refEntity->$refName->$refMappedBy !== NULL) {
						$this->update($refEntity->$refName);
					} else {
						$refEntity->$refMappedBy = $this->recRevertInsert($refEntity->$refName, $ref, $refName, $refEntity);
						$refEntity->$refName->$refMappedBy = $refEntity->$refMappedBy;
					}
				} else if ($ref->canBeNULL === FALSE) {
					throw new RepositoryException("Property " . $ref->targetEntity . ":" . $refMappedBy . " cannot be NULL.");
				}
			}
		}
		return $refEntity;
	}

	/**
	 * @param Entity $entity
	 * @param stdClass $reference
	 * @param string $name
	 * @param Entity $parent
	 * @return mixed
	 */
	private function recRevertInsert(Entity $entity, $reference, $name, Entity $parent) {
		$refReferences = $entity->getReferences();
		$entity = $this->beforeInsert($parent, $refReferences, $name);
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
	private function checkLoop(Entity $entity) {
		foreach($this->protectLoop as $loop) {
			if (EntityReflexion::getTable($loop) == EntityReflexion::getTable($entity) && $entity->getPrimary() == $loop->getPrimary()) {
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

	/** Nastavení ochrany proti zacyklení
	 * @param array $loop
	 */
	public function setLoop(array $loop) {
		$this->originalLoop = $loop;
		$this->protectLoop = $loop;
	}

	/**
	 * @return bool
	 */
	public function isEmpty() {
		return count($this->rows) > 0 ? FALSE : TRUE;
	}


	/*	 * ******************* interface \IteratorAggregate *****************  */

	public function getIterator() {
		if ($this->selection && count($this->rows) == 0) {
			$this->fetchAll();
		}
		return count($this->rows) > 0 ? new \ArrayIterator($this->rows) : new \ArrayIterator();
	}

	/*	 * ******************* interface \Countable *****************  */

	/** Count
	 * 
	 * @param string $column
	 * @return int
	 */
	public function count($column = NULL) {
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

?>
