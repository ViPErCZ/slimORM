<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 13:31
 */

namespace Model\Library\Entity;

use Model\Library\BookAttachmentRepository;
use slimORM\Entity\Entity;

/**
 * Class Book
 * @table book
 * @package Model\Library\Entity
 */
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
	 * @var BookAttachmentRepository
	 */
	protected $attachments;

	/**
	 * @param int $libraryID
	 */
	public function setLibraryID($libraryID)
	{
		$this->libraryID = $libraryID;
	}

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
	 * @throws \Nette\InvalidArgumentException
	 * @throws \ErrorException
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	public function addAttachment(Attachment $attach) {
		$bookAttachment = new BookAttachment();
		$bookAttachment->setAttachment($attach);
		$this->attachments = $this->entityManager->getRepository(BookAttachment::class);
		$this->attachments->push($bookAttachment);
	}

	/**
	 * @return BookAttachmentRepository
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return \Model\Library\Entity\Author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @return int
	 */
	public function getBookID() {
		return $this->bookID;
	}

	/**
	 * @return \Model\Library\Entity\Library
	 */
	public function getLibrary() {
		return $this->library;
	}

	/**
	 * @return int
	 */
	public function getLibraryID() {
		return $this->libraryID;
	}

} 