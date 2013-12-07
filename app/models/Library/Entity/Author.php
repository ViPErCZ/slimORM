<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:21
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

class Author extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $authorID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @reference book
	 * @var Book
	 */
	protected $book;

	/**
	 * @param int $authorID
	 */
	public function setAuthorID($authorID)
	{
		$this->authorID = (int)$authorID;
	}

	/**
	 * @param Book $book
	 */
	public function setBook(Book $book)
	{
		$this->book = $book;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @return \Model\Library\Entity\Book
	 */
	public function getBook() {
		return $this->oneToOne("book", "book", "bookID", '\Model\Library\Entity\Book');
	}

} 