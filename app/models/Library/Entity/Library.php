<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 13:11
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

class Library extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $libraryID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * reference librarian
	 * @var Librarian
	 */
	protected $librarian;

	/**
	 * @reference book
	 * @var Book
	 */
	protected $books;

	/**
	 * @param Book $books
	 * @return $this
	 */
	public function addBook(Book $books) {
		$this->books[] = $books;
		return $this;
	}

	/**
	 * @return array of Book entity
	 */
	public function getBooks() {
		return $this->oneToMany("books", "book", "libraryID", "Model\Library\Entity\Book");
	}

	/**
	 * @param \Model\Library\Entity\Librarian $librarian
	 * @return $this
	 */
	public function setLibrarian(Librarian $librarian) {
		$this->librarian = $librarian;
		return $this;
	}

	/**
	 * @param int $libraryID
	 * @return $this
	 */
	public function setLibraryID($libraryID) {
		$this->libraryID = $libraryID;
		return $this;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name) {
		$this->name = (string)$name;
		return $this;
	}

} 