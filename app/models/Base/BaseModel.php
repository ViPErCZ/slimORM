<?php
/**
 * User: Martin Chudoba
 * Date: 10.3.14
 * Time: 19:07
 */

namespace Model\Base;


use slimORM\Entity\Entity;
use slimORM\EntityManager;

abstract class BaseModel {

	/** @var \slimORM\EntityManager */
	protected $entityManager;

	/**
	 * Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}
} 