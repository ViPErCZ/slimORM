<?php

namespace slimORM\Entity;
use Nette\Database\Table\ActiveRow;
use Nette\Object;
use Nette\ObjectMixin;
use Nette\Reflection\ClassType;
use slimORM\Entity\Exception\EntityException;

/**
 * Základní třída pro entitu
 *
 * @author Martin Chudoba
 */
abstract class Entity extends Object {

	/** @var ActiveRow */
	protected $row;

	/** @var array */
	private $references;

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
    final public function getReferences() {
		if (count($this->references) == 0) {
			$reflection = ClassType::from(get_class($this));

			foreach ($reflection->getProperties() as $property) {
				if ($property->hasAnnotation("reference") === TRUE) {
					$ref = new \stdClass();
					$ref->property = $property->getName();
					$args = $property->getAnnotation("reference");
					if (is_string($args)) {
						$ref->table = $args;
					} else {
						$ref->table = $ref->property;
					}

					if ($property->hasAnnotation("OneToOne") === TRUE) {
						$ref->linkage = "OneToOne";
						$linkage = $property->getAnnotation("OneToOne");
						$ref->targetEntity = $linkage->targetEntity;
						$ref->key = $linkage->mappedBy;
						$ref->canBeNULL = isset($linkage->canBeNULL) ? (boolean)$linkage->canBeNULL : FALSE;
					} elseif ($property->hasAnnotation("OneToMany") === TRUE) {
						$ref->linkage = "OneToMany";
						$linkage = $property->getAnnotation("OneToMany");
						$ref->targetEntity = $linkage->targetEntity;
						$ref->key = $linkage->mappedBy;
						$ref->canBeNULL = isset($linkage->canBeNULL) ? (boolean)$linkage->canBeNULL : FALSE;
					} elseif ($property->hasAnnotation("ManyToOne") === TRUE) {
						$ref->linkage = "ManyToOne";
						$linkage = $property->getAnnotation("ManyToOne");
						$ref->targetEntity = $linkage->targetEntity;
						$ref->key = $linkage->mappedBy;
						$ref->canBeNULL = isset($linkage->canBeNULL) ? (boolean)$linkage->canBeNULL : FALSE;
					} elseif ($property->hasAnnotation("ManyToMany") === TRUE) {
						$ref->linkage = "ManyToMany";
						$linkage = $property->getAnnotation("ManyToMany");
						$ref->targetEntity = $linkage->targetEntity;
						$ref->key = $linkage->mappedBy;
						$ref->canBeNULL = isset($linkage->canBeNULL) ? (boolean)$linkage->canBeNULL : FALSE;
					} else {
						throw new EntityException("Reference \"". $this->getReflection() . "::$ref->property\" has no set linkage annotation type.");
					}

					$this->references[$ref->property] = $ref;
				}
			}
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
				$this->$propertyName = $arr = array();
				foreach($items as $item) {
					if (is_array($item->getPrimary())) {
						$arr[] = new $entityClass($item);
					} else {
						$arr[$item->getPrimary()] = new $entityClass($item);
					}
				}
				$this->$propertyName = $arr;
			} else
				$this->$propertyName = array();
		} else if ($this->$propertyName === NULL && $this->row === NULL)
			$this->$propertyName = array();

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
		if ($this->row) {
			$row = $this->row->ref($table, $referenceKey);
			if ($row)
				$this->$propertyName = new $entityClass($row);
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
		$reflection = ClassType::from(get_class($this));
		$arr = array();
		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation("read") === TRUE || $property->hasAnnotation("column") === TRUE) {
				$name = $property->getName();
				$arr[$property->getName()] = $this->$name;
			}
		}
		return $arr;
	}

	/**
	 * @return array
	 */
	final public function getColumns() {
		$reflection = ClassType::from(get_class($this));
		$arr = array();
		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation("read") === TRUE || $property->hasAnnotation("column") === TRUE) {
				$arr[] = $property->getName();
			}
		}
		return $arr;
	}
	
	/**
	 * 
	 */
	final protected function evaluated() {
		$reflection = ClassType::from(get_class($this));
		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation("read") === TRUE || $property->hasAnnotation("column") === TRUE) {
				$name = $property->getName();
				if ($this->row) {
					$this->$name = $this->row->$name;
				}
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
		/*if ($this->row !== NULL) {
			$this->row->$name = $this->$name;
		}*/
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
