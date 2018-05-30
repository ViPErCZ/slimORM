<?php

namespace Model\Library\Entity;

use slimORM\Entity\Entity;
use Model\Library\Entity\Attachment;

/**
 * Class BookAttachment
 * @table book_has_attachment
 * @package Model\Library\Entity
 */
class BookAttachment extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $book_has_attachmentID;

	/**
	 * @column
	 * @var int
	 */
	protected $bookID;

	/**
	 * @column
	 * @var int
	 */
	protected $attachmentID;

	/**
	 * @reference attachment
	 * @OneToOne(targetEntity="Model\Library\Entity\Attachment", mappedBy="attachmentID")
	 * @var Attachment
	 */
	protected $attachment;

	/**
	 * @reference book
	 * @OneToOne(targetEntity="Model\Library\Entity\Book", mappedBy="bookID")
	 * @var Book
	 */
	protected $book;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		$this->attachment = $this->oneToOne('attachment', 'attachment', 'attachmentID', Attachment::class);
		if ($this->attachment) {
			return $this->attachment->name;
		} else {
			return null;
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name): void {
		$this->name = $name;
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
	 * @return int|null
	 */
	public function getAttachmentID(): ?int {
		return $this->attachmentID;
	}

	/**
	 * @param int $attachmentID
	 */
	public function setAttachmentID($attachmentID): void {
		$this->attachmentID = $attachmentID;
	}

	/**
	 * @return Attachment|null
	 */
	public function getAttachment(): ?Attachment {
		return $this->attachment;
	}

	/**
	 * @param Attachment|null $attachment
	 */
	public function setAttachment(Attachment $attachment = null): void {
		$this->attachment = $attachment;
	}

	/**
	 * @return int|null
	 */
	public function getBook_has_attachmentID(): ?int {
		return $this->book_has_attachmentID;
	}

	/**
	 * @param int $book_has_attachmentID
	 */
	public function setBook_has_attachmentID($book_has_attachmentID): void {
		$this->book_has_attachmentID = $book_has_attachmentID;
	}

	/**
	 * @return Book|null
	 */
	public function getBook(): ?Book {
		return $this->book;
	}

} 