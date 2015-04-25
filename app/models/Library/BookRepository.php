<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 6.6.14
 * Time: 13:32
 */

namespace Model\Library;


use Nette\Utils\Paginator;
use slimORM\AbstractRepository;
use Model\Library\Entity\Book;
use slimORM\EntityManager;

class BookRepository extends AbstractRepository {
	/** Entity Class */
	const ENTITY = '\Model\Library\Entity\Book';

	/** Constructor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->entity = BookRepository::ENTITY;
	}

	/**
	 * @param string $key
	 * @return Book|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return BookRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return Book|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Book $book
	 * @return BookRepository
	 */
	public function push(Book $book) {
		return parent::push($book);
	}

	/**
	 * @return Book[]
	 */
	public function fetchAll() {
		return parent::fetchAll();
	}
} 