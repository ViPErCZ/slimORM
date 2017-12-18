<?php
/**
 * User: Martin
 * Date: 6.12.13
 * Time: 13:20
 */

namespace Model\Library;


use Model\Library\Entity\Author;
use Nette\Utils\Paginator;
use slimORM\AbstractRepository;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class AuthorRepository extends AbstractRepository {

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
	 * @return bool
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $library
	 * @return \slimORM\BaseRepository
	 */
	public function push(Entity $library) {
		return parent::push($library);
	}

	/**
	 * @return Author
	 */
	public function fetch() {
		return parent::fetch();
	}

} 