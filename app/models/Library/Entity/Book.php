<?php

namespace Model\Library\Entity;

use Model\Library\BookAttachmentRepository;
use slimORM\BaseRepository;
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
	 * @param Attachment $attach
	 * @throws \ErrorException
	 * @throws \Throwable
	 * @throws \slimORM\Entity\Exception\EntityException
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	public function addAttachment(Attachment $attach): void {
		$bookAttachment = new BookAttachment();
		$bookAttachment->setAttachment($attach);
		$this->attachments = $this->entityManager->getRepository(BookAttachment::class);
		$this->attachments->push($bookAttachment);
	}

	/**
	 * @return BookAttachmentRepository|null
	 */
	public function getAttachments(): ?BaseRepository {
		return $this->attachments;
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
	 * @return Author|null
	 */
	public function getAuthor(): ?Author {
		return $this->author;
	}

	/**
	 * @param Author $author
	 */
	public function setAuthor(Author $author): void {
		$this->author = $author;
	}

	/**
	 * @return int|null
	 */
	public function getBookID(): ?int {
		return $this->bookID;
	}

	/**
	 * @param int $bookID
	 */
	public function setBookID($bookID): void {
		$this->bookID = $bookID;
	}

	/**
	 * @return Library|null
	 */
	public function getLibrary(): ?Library {
		return $this->library;
	}

	/**
	 * @param Library $library
	 */
	public function setLibrary(Library $library): void {
		$this->library = $library;
		if ($this->library->toRow()) {
			$this->libraryID = $library->libraryID;
		}
	}

	/**
	 * @return int|null
	 */
	public function getLibraryID(): ?int {
		return $this->libraryID;
	}

	/**
	 * @param int $libraryID
	 */
	public function setLibraryID($libraryID): void {
		$this->libraryID = $libraryID;
	}

} 