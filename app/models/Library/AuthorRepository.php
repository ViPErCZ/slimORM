<?php
/**
 * User: Martin
 * Date: 6.12.13
 * Time: 13:20
 */

namespace Model\Library;


use Model\Base\BaseModel;
use Model\Library\Entity\Author;
use Nette\Utils\Paginator;
use slimORM\EntityManager;

class AuthorRepository extends BaseModel {

	const ENTITY = 'Model\Library\Entity\Author';

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = AuthorRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Author|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return AuthorRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @param bool $needTransaction
	 * @param Author $library
	 * @return Author|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Author $library
	 * @return AuthorRepository
	 */
	public function push(Author $library) {
		return parent::push($library);
	}

	/**
	 * @return Author
	 */
	public function fetch() {
		return parent::fetch();
	}

	/**
	 * @param $condition
	 * @param array $parameters
	 * @return AuthorRepository
	 */
	public function where($condition, $parameters = array()) {
		return parent::where($condition, $parameters = array());
	}
} 