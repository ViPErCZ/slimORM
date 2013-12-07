<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 13:31
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

class Book extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $bookID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @reference author
	 * @var Author
	 */
	protected $author;

	/**
	 * @reference library
	 * @var Library
	 */
	protected $library;

	/**
	 * @param Author $author
	 */
	public function setAuthor(Author $author)
	{
		$this->author = $author;
	}

	/**
	 * @param int $bookID
	 */
	public function setBookID($bookID)
	{
		$this->bookID = $bookID;
	}

	/**
	 * @param Library $library
	 */
	public function setLibrary(Library $library)
	{
		$this->library = $library;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @return \Model\Library\Entity\Author
	 */
	public function getAuthor() {
		return $this->manyToOne("author", "author", "bookID", 'Model\Library\Entity\Author');
	}

	/**
	 * @return \Model\Library\Entity\Library
	 */
	public function getLibrary() {
		return $this->manyToOne("library", "library", "libraryID", '\Model\Library\Entity\Library');
	}

} 