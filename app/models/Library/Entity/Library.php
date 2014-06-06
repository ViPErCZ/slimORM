<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 13:11
 */

namespace Model\Library\Entity;

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
	 * @var \Model\Library\BookRepository
	 */
	protected $books;

	/**
	 * @param Book $book
	 * @return $this
	 */
	public function addBook(Book $book)
	{
		$book->setLibrary($this);
		$this->books = $this->entityManager->getRepository("\Model\Library\Entity\Book");
		$this->getBooks()->push($book);
		return $this;
	}

	/**
	 * @param \Model\Library\Entity\Librarian $librarian
	 * @return $this
	 */
	public function setLibrarian(Librarian $librarian)
	{
		$this->librarian = $librarian;
		return $this;
	}

	/**
	 * @param int $libraryID
	 * @return $this
	 */
	public function setLibraryID($libraryID)
	{
		$this->libraryID = $libraryID;
		return $this;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \Model\Library\Entity\Librarian
	 */
	public function getLibrarian()
	{
		return $this->librarian;
	}

	/**
	 * @return \Model\Library\BookRepository
	 */
	public function getBooks()
	{
		return $this->books;
	}

	/**
	 * @return int
	 */
	public function getLibraryID()
	{
		return $this->libraryID;
	}

} 