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
	 * @column
	 * @var int
	 */
	protected $libraryID;

	/**
	 * @reference author
	 * @OneToOne(targetEntity="Model\Library\Entity\Author", mappedBy="bookID")
	 * @var Author
	 */
	protected $author;

	/**
	 * @reference library
	 * @ManyToOne(targetEntity="Model\Library\Entity\Library", mappedBy="libraryID")
	 * @var Library
	 */
	protected $library;

	/**
	 * @reference book_has_attachment
	 * @ManyToMany(targetEntity="Model\Library\Entity\BookAttachment", mappedBy="bookID")
	 * @var \Model\Library\Entity\BookAttachment
	 */
	protected $attachments;

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
		if ($this->library->toRow()) {
			$this->libraryID = $library->libraryID;
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

	/**
	 * @param Attachment $attach
	 */
	public function addAttachment(Attachment $attach) {
		$this->attachments[] = $attach;
	}

} 