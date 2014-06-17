<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 11.3.14
 * Time: 19:42
 */

namespace slimORM\Reflexion;

use Nette\Reflection\ClassType;
use Nette\StaticClassException;
use slimORM\Entity\Exception\EntityException;

class EntityReflexion
{
	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct() {
		throw new StaticClassException;
	}

	/**
	 * Vrací reference a jejich parametry
	 * @param $className
	 * @return array
	 * @throws \slimORM\Entity\Exception\EntityException
	 */
	public static function getReferences($className)
	{
		$references = array();
		$reflection = ClassType::from($className);

		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation("reference") === TRUE) {
				$ref = new \stdClass();
				$ref->property = $property->getName();
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
					throw new EntityException("Reference \"" . $className . "::$ref->property\" has no set linkage annotation type.");
				}

				$references[$ref->property] = $ref;
			}
		}
		return $references;
	}

	/**
	 * @param $className
	 * @return array
	 */
	public static function getColumns($className)
	{
		$reflection = ClassType::from($className);
		$arr = array();
		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation("read") === TRUE || $property->hasAnnotation("column") === TRUE) {
				$doc = $property->getAnnotations();
				unset($doc['column']);
				unset($doc['read']); //deprecated
				$arr[] = array(
					"name"			=> $property->getName(),
					"annotations"	=> $doc
				);
			}
		}
		return $arr;
	}

	/** Get parent class
	 * @param $className
	 * @return null|string
	 */
	public static function getParent($className) {
		$reflection = ClassType::from($className);
		$parent = $reflection->getParentClass();
		return $parent ? $parent->getName() : $parent;
	}

	/**
	 * @param $className
	 * @return null|string
	 */
	public static function getTable($className) {
		$reflection = ClassType::from($className);
		if ($reflection->hasAnnotation("table")) {
			return $reflection->getAnnotation("table");
		} else
			return NULL;
	}

	/**
	 * @param $className
	 * @return string
	 */
	public static function getFile($className) {
		$reflection = ClassType::from($className);
		return $reflection->getFileName();
	}
} 