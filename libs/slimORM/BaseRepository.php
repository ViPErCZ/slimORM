<?php

namespace slimORM;

use Nette\Database\Connection;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\Exceptions\RepositoryException;

/**
 * Description of BaseRepository
 *
 * @author Martin Chudoba
 */
abstract class BaseRepository implements \IteratorAggregate, \Countable {

	/** @var Connection */
	protected $database;

	/** @var string */
	protected $entity;

	/** @var string */
	protected $table;

	/** @var array */
	protected $rows;

	/** @var \Nette\Database\Table\Selection */
	protected $selection;

	/** @var array of primary key values */
	protected $keys = array();

	/** Konstruktor
	 * 
	 * @param Connection $connection
	 * @param string $tableName
	 * @param string $entity
	 */
	public function __construct(Connection $connection, $tableName, $entity) {
		$this->database = $connection;
		$this->table = (string) $tableName;
		$this->entity = (string) $entity;
		$this->selection = NULL;
	}

	/** Sestavení SQL dotazu
	 * 
	 * @param Paginator $paginator
	 * @return \Nette\Database\Table\Selection
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
		$endEntity = NULL;
		if (is_string($primary)) {
			if ($refresh) {
				$endEntity = new $this->entity($this->buildSql()->get($primary));
			} else {
				$endEntity = new $this->entity($row);
			}
			$this->rows[$primary] = $endEntity;
		} else {
			throw new RepositoryException("Table \"$this->table\" has no primary key.");
		}

		return $endEntity;
	}

	/** Vrací poslední vložené ID
	 * 
	 * @return int|null
	 * @throws RepositoryException
	 */
	public function getLastInsertID() {
		if (count($this->rows) > 0) {
			$end = end($this->rows);
			if ($end->toRow()->getPrimary()) {
				return (int) $end->toRow()->getPrimary();
			} else {
				throw new RepositoryException("Table \"" . self::TABLE . "\" does not have a primary key.");
			}
		} else {
			return NULL;
		}
	}

	/** Najde položku podle primárního klíče
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
				$this->rows[$item->getPrimary()] = new $this->entity($item);
				return $this->rows[$item->getPrimary()];
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
	 * @param string $having
	 * @return BaseRepository
	 * @throws RepositoryException
	 */
	public function group($columns, $having = NULL) {
		if ($this->selection)
			$this->selection->group($columns, $having);
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

	/** Odešle se SQL
	 * 
	 * @return array|NULL
	 */
	public function fetchAll() {
		if ($this->selection) {
			foreach ($this->selection as $entity) {
				if (is_string($entity->getPrimary()) || is_int($entity->getPrimary()))
					$this->rows[$entity->getPrimary()] = new $this->entity($entity);
				else {
					$this->rows[] = new $this->entity($entity);
				}
			}
		}

		return $this->rows;
	}

	/** Vrací výsledky po jednom
	 * 
	 * @return mixed
	 */
	public function fetch() {
		if ($this->selection && count($this->rows) == 0) {
			$this->fetchAll();
		}
		return current($this->rows);
	}

	/** Vytvoří select
	 * 
	 * @param Paginator $paginator
	 * @return BaseRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$this->rows = array();
		$this->selection = $this->buildSql($paginator);
		return $this;
	}

	/** Delete
	 * 
	 * @param mixed $key
	 * @throws \PDOException
	 */
	public function delete($key) {
		try {
			$this->database->beginTransaction();
			$row = $this->get($key);
			if ($row) {
				$row->toRow()->delete();
				unset($this->rows[$key]);
				$this->database->commit();
			} else {
				throw new \PDOException("Item with primary key " . $key . " not found.");
			}
		} catch (\PDOException $e) {
			$this->database->rollBack();
			throw new \PDOException($e->getMessage());
		}
	}

	/** Uloží entitu
	 *
	 * @param boolean $needTransaction
	 * @param Entity $entity
	 * @throws \PDOException
	 * @return Entity|TRUE
	 */
	public function save($needTransaction = TRUE, Entity $entity = NULL) {
		try {
			if ($needTransaction)
				$this->database->beginTransaction();
			if ($entity) {
				if ($entity->toRow()) {
					$entity->__update();
					if ($needTransaction)
						$this->database->commit();
					return $entity;
				} else {
					$returnEntity = $this->insert($entity);
					if ($needTransaction)
						$this->database->commit();
					return $returnEntity;
				}
			} else {
				if (is_array($this->rows)) {
					foreach ($this->rows as $key => $row) {
						if ($row->toRow()) {
							$row->__update();
						} else {
							$returnEntity = $this->insert($row);
							unset($this->rows[$key]);
							$this->rows[$returnEntity->getPrimary()] = $returnEntity;
						}
					}
				}
				if ($needTransaction)
					$this->database->commit();
				return TRUE;
			}
		} catch (\PDOException $e) {
			if ($needTransaction)
				$this->database->rollBack();
			throw new \PDOException($e->getMessage());
		}
	}

	/** Vloží entitu do pole
	 * @param Entity $entity
	 */
	public function push(Entity $entity) {
		if ($entity->toRow()) {
			$this->rows[$entity->getPrimary()] = $entity;
		} else {
			$this->rows[] = $entity;
		}
	}

	/**
	 * 
	 * @return NULL|\Nette\Database\Table\Selection
	 */
	public function getSelection() {
		return $this->selection;
	}

	/** Abstraktní metody */
	abstract protected function insert(Entity $entity);


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
		if ($this->selection) {
			return $this->selection->count($column);
		}
		else
			return count($this->rows);
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
