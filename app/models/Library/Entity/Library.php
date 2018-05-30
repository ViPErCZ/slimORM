<?php

namespace Model\Library\Entity;

use Model\Library\BookRepository;
use slimORM\BaseRepository;
use slimORM\Entity\Entity;

/**
 * Class Library
 * @table library
 * @package Model\Library\Entity
 */
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
	 * @reference librarian
	 * @OneToOne(targetEntity="\Model\Library\Entity\Librarian", mappedBy="libraryID")
	 * @var Librarian
	 */
	protected $librarian;

	/**
	 * @reference book
	 * @OneToMany(targetEntity="\Model\Library\Entity\Book", mappedBy="libraryID")
	 * @var BookRepository
	 */
	protected $books;

	/**
	 * @param Book $book
	 */
	public function addBooks(Book $book): void {
		$book->setLibrary($this);
		$this->books[] = $book;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name): void {
		$this->name = (string)$name;
	}

	/**
	 * @return Librarian|null
	 */
	public function getLibrarian(): ?Librarian {
		return $this->librarian;
	}

	/**
	 * @param Librarian|null $librarian
	 */
	public function setLibrarian(Librarian $librarian = null): void {
		$this->librarian = $librarian;
	}

	/**
	 * @return BookRepository|null
	 */
	public function getBooks(): ?BaseRepository {
		return $this->books;
	}

	/**
	 * @return int|null
	 */
	public function getLibraryID(): ?int {
		return $this->libraryID;
	}

	/**
	 * @param $libraryID
	 */
	public function setLibraryID($libraryID): void {
		$this->libraryID = $libraryID;
	}

} 