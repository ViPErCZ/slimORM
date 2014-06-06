<?php
/**
 * User: Martin
 * Date: 11.12.13
 * Time: 9:12
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

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
	 * @return string
	 */
	public function getName()
	{
		$this->attachment = $this->oneToOne("attachment", "attachment", "attachmentID", 'Model\Library\Entity\Attachment');
		if ($this->attachment instanceof Attachment)
			return $this->attachment->name;
	}

	/**
	 * @return int
	 */
	public function getBookID()
	{
		return $this->bookID;
	}

	/**
	 * @return int
	 */
	public function getAttachmentID()
	{
		return $this->attachmentID;
	}

	/**
	 * @return \Model\Library\Entity\Attachment
	 */
	public function getAttachment()
	{
		return $this->attachment;
	}

	/**
	 * @param int $book_has_attachmentID
	 */
	public function setBookHasAttachmentID($book_has_attachmentID) {
		$this->book_has_attachmentID = $book_has_attachmentID;
	}

	/**
	 * @return int
	 */
	public function getBook_has_attachmentID() {
		return $this->book_has_attachmentID;
	}


	/**
	 * @param int $attachmentID
	 */
	public function setAttachmentID($attachmentID)
	{
		$this->attachmentID = $attachmentID;
	}

	/**
	 * @param int $bookID
	 */
	public function setBookID($bookID)
	{
		$this->bookID = $bookID;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param \Model\Library\Entity\Attachment $attachment
	 */
	public function setAttachment($attachment)
	{
		$this->attachment = $attachment;
	}


	/**
	 * @return \Model\Library\Entity\Book
	 */
	public function getBook()
	{
		return $this->book;
	}

} 