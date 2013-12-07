<?php

namespace slimORM\Entity;
use Model\Base\Entity\Exceptions\EntityException;
use Nette\Database\Table\ActiveRow;
use Nette\Object;
use Nette\ObjectMixin;
use Nette\Reflection\ClassType;

/**
 * Základní třída pro entitu
 *
 * @author Martin Chudoba
 */
abstract class Entity extends Object {

	/** @var ActiveRow */
	protected $row;

	/** Konstruktor
	 * 
	 * @param ActiveRow $row
	 */
	public function __construct(ActiveRow $row = NULL) {
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
		
			if ($items->count() > 0) {
				$this->$propertyName = $arr = array();
				foreach($items as $item) {
					$arr[$item->getPrimary()] = new $entityClass($item);
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
	final protected function oneToOne($propertyName, $table, $referenceKey, $entityClass) {
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
	final protected function manyToOne($propertyName, $table, $relatedKey, $entityClass) {
		if ($this->row) {
			$items = $this->row->related($table, $relatedKey);
			if ($items->count() > 0) {
				$this->$propertyName = new $entityClass($items->fetch());
			}
		}
		return $this->$propertyName;
	}

	/**
	 * @return mixed
	 */
	final public function getPrimary() {
		return $this->row ? $this->row->getPrimary() : NULL;
	}

	/** Nastaví hodnoty všem proměnným ze seznamu *read
	 * 
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

	/** Update Entity
	 * 
	 * @throws EntityException
	 */
	final public function __update() {
		if ($this->row) {
			$this->updateActiveRow();
			$this->row->update();
			$reflection = ClassType::from(get_class($this));
			foreach ($reflection->getProperties() as $property) {
				if ($property->hasAnnotation("reference") === TRUE) {
					$name = $property->getName();
					if (is_array($this->$name)) {
						foreach ($this->$name as $item) {
							if ($item->toRow())
								$item->__update();
							else {
								$table = $property->getAnnotation("reference");
								if (is_string($table))
									$this->__insert($table, $item);
								else
									throw new EntityException("Property \"reference\" has no table value.");
							}
						}
					} else if ($this->$name) {
						if ($this->$name->toRow())
							$this->$name->__update();
						else {
							$table = $property->getAnnotation("reference");
							if (is_string($table))
								$this->__insert($table, $this->$name);
							else
								throw new EntityException("Property \"reference\" has no table value.");
						}
					}
				}
			}
		}
		else
			throw new EntityException("Entity cannot update before reading.");
	}

	/** Insert
	 * 
	 * @param string $table
	 * @param Entity $entity
	 * @return ActiveRow
	 */
	final public function __insert($table, Entity $entity) {
		return $this->row->getTable()->getConnection()->table((string)$table)->insert($entity->toArray());
	}
	
	/**
	 * 
	 */
	final protected function updateActiveRow() {
		if ($this->row) {
			$reflection = ClassType::from(get_class($this));
			foreach ($reflection->getProperties() as $property) {
				if ($property->hasAnnotation("read") === TRUE || $property->hasAnnotation("column") === TRUE) {
					$name = $property->getName();
					if ($this->row->$name != $this->$name) {
						$this->row->$name = $this->$name;
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
		ObjectMixin::set($this, $name, $value);
		if ($this->row !== NULL) {
			$this->row->$name = $this->$name;
		}
	}

	/**
	 * Returns property value. Do not call directly.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name) {
		try {
			$getter = "get".ucfirst($name);
			$reflection = ClassType::from(get_class($this));
			$reflection->getMethod($getter);
			$val = $this->$getter();
			return $val;
		} catch (\ReflectionException $e) {
			$val = $this->$name;
			return $val;
		}
	}

}

?>
