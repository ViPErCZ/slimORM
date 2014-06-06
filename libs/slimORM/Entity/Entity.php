<?php

namespace slimORM\Entity;
use Nette\Database\Table\ActiveRow;
use Nette\Object;
use Nette\ObjectMixin;
use Nette\Reflection\ClassType;
use slimORM\Entity\Exception\EntityException;
use slimORM\EntityManager;
use slimORM\Reflexion\EntityReflexion;

/**
 * Základní třída pro entitu
 *
 * @author Martin Chudoba
 */
abstract class Entity extends Object {

	/** @var ActiveRow */
	protected $row;

	/** @var array */
	protected $references;

	/** @var EntityManager */
	protected $entityManager;

	/** Konstruktor
	 * 
	 * @param ActiveRow $row
	 */
	public function __construct(ActiveRow $row = NULL) {
		$this->references = array();
		$this->row = $row;
		if ($this->row)
			$this->evaluated();
	}

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}


	/**
	 * @return ActiveRow
	 */
	final public function toRow() {
		return $this->row;
	}

    /**
     * Vrací reference a jejich parametry
     * @return array
     * @throws Exception\EntityException
     */
    public function getReferences() {
		if (count($this->references) == 0) {
			$this->references = EntityReflexion::getReferences(get_class($this));
		}
		return $this->references;
	}
	
	/**
	 * 
	 * @param string $propertyName
	 * @param string $table
	 * @param string $relatedKey
	 * @param string $entityClass
	 * @return array
	 */
	final protected function &oneToMany($propertyName, $table, $relatedKey, $entityClass) {
		if ($this->$propertyName === NULL && $this->row) {
			$items = $this->row->related($table, $relatedKey);
		
			if ($items->count('*') > 0) {
				if ($this->entityManager) {
					return $this->entityManager->getRepository($entityClass)->read()->where($relatedKey, $this->row->getPrimary());
				} else {
					throw new EntityException("Please set EntityManager instance to " . get_class($this) . " class.");
				}
			} else
				$this->$propertyName = NULL; //array();
		} else if ($this->$propertyName === NULL && $this->row === NULL)
			$this->$propertyName = NULL; //array();

		return $this->$propertyName;
	}
	
	/**
	 * 
	 * @param string $propertyName
	 * @param string $table
	 * @param string $referenceKey
	 * @param string $entityClass
	 * @return instance of $entityClass
	 */
	final protected function &oneToOne($propertyName, $table, $referenceKey, $entityClass) {
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
	 * @param string $propertyName
	 * @param string $table
	 * @param string $relatedKey
	 * @param string $entityClass
	 * @return instance of $entityClass
	 */
	final protected function &manyToOne($propertyName, $table, $relatedKey, $entityClass) {
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
     * Nastaví hodnoty všem proměnným ze seznamu *read
     * @param array $values
     * @throws MemberAccessException
     */
    final public function setValues(array $values) {
		if (is_array($values)) {
			$class = get_class($this);
			foreach (array_keys($this->toArray()) as $key) {
				$uname = ucfirst($key);
				$method = 'set' . $uname;
				$reflection = ClassType::from($class);
				if (isset($values[$key])) {
					if ($reflection->hasMethod($method)) { // property setter
						$this->$method($values[$key]);
					} else if ($reflection->hasProperty($key)) { // unsetted property
						$this->$key = $values[$key];
						if ($this->row !== NULL) {
							$this->row->$key = $values[$key];
						}
					} else {
						$type = $reflection->hasMethod('get' . $uname) || $reflection->hasMethod('is' . $uname) ? 'a read-only' : 'an undeclared';
						throw new MemberAccessException("Cannot write to $type property $class::\$$key.");
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	final public function toArray() {
		$arr = array();
		foreach ($this->getColumns() as $property) {
			$name = $property['name'];
			$getter = "get" . ucfirst($name);
			$arr[$name] = $this->$getter();
		}
		/*var_dump($arr);
		var_dump($this);*/
		return $arr;
	}

	/**
	 * @return array
	 */
	final public function getColumns() {
		return EntityReflexion::getColumns(get_class($this));
	}
	
	/**
	 * 
	 */
	final protected function evaluated() {
		if ($this->row) {
			foreach ($this->getColumns() as $property) {
				$this->$property['name'] = $this->row->$property['name'];
			}
		}
	}

	/**
	 * Sets value of a property. Do not call directly.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name, $value) {
		$rp = new \ReflectionProperty(get_class($this), $name);
		if ($rp->getName() === $name) {
			$this->$name = $value;
		} else {
			ObjectMixin::set($this, $name, $value);
		}
	}

    /**
     * Returns property value. Do not call directly.
     * @param $name
     * @return array|callable|null|instance
     */
    public function &__get($name) {
		$getter = "get".ucfirst($name);
		$reflection = ClassType::from(get_class($this));
		if ($reflection->hasMethod($getter)) {
			$reflection->getMethod($getter);
			$val = $this->$getter();
			return $val;
		} else if ($reflection->hasProperty($name)) {
			$references = $this->getReferences();
			if ($this->$name === NULL) {
				if (array_key_exists($name, $references) === TRUE && $this->row) {
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
							if ($this->row->getTable()->getPrimary(TRUE) === $references[$name]->key) {
								$val = $this->manyToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
							} else {
								$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
							}
							break;
						case 'ManyToMany':
							if ($this->row->getTable()->getPrimary(TRUE) === $references[$name]->key) {
								$val = $this->oneToMany($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
							} else {
								$val = $this->oneToOne($name, $references[$name]->table, $references[$name]->key, $references[$name]->targetEntity);
							}
							break;
					}
					$this->$name = $val;
				}
			}
		}
		return $this->$name;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->$name);
	}

}

?>
