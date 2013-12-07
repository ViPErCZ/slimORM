<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:54
 */

namespace Model\Library;


use Model\Library\Entity\Library;
use Nette\Database\Connection;
use slimORM\BaseRepository;
use slimORM\Entity\Entity;
use slimORM\Exceptions\RepositoryException;

class LibraryRepository extends BaseRepository {

	const TABLE = "library";
	const ENTITY = 'Model\Library\Entity\Library';

	/** Construct
	 * @param Connection $database
	 */
	public function __construct(Connection $database) {
		parent::__construct($database, LibraryRepository::TABLE, LibraryRepository::ENTITY);
	}

	/**
	 * @param Entity $library
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	protected function insert(Entity $library) {
		if ($library instanceof Library) {
			$row = $this->buildSql()->insert($library->toArray());
			$entity = $$this->createEntity($row, TRUE);
			if (count($library->getBooks()) > 0) {
				foreach ($library->getBooks() as $book) {
					$entity->addBook($book);
				}
				$this->save(FALSE, $entity);
			}
			return $entity;
		} else {
			throw new RepositoryException('Expected instance of Model\Library\Entity\Library. Given ' . gettype($library) . ".");
		}
	}
} 