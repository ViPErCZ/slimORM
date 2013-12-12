<?php
/**
 * User: Martin
 * Date: 11.12.13
 * Time: 9:12
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

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
} 