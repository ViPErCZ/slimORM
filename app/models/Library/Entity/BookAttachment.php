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
	 * @return \Model\Library\Entity\Book
	 */
	public function getBook()
	{
		return $this->book;
	}

} 