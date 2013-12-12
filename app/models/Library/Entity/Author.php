<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:21
 */

namespace Model\Library\Entity;


use Model\Contact\Entity\Contact;
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
	 * @column
	 * @var int
	 */
	protected $bookID;

	/**
	 * @column
	 * @var int
	 */
	protected $languageID;

	/**
	 * @column
	 * @var int
	 */
	protected $contactID;

	/**
	 * @reference book
	 * @OneToOne(targetEntity="Model\Library\Entity\Book", mappedBy="bookID")
	 * @var Book
	 */
	protected $book;

	/**
	 * @reference language
	 * @OneToOne(targetEntity="Model\Library\Entity\Language", mappedBy="languageID")
	 * @var Language
	 */
	protected $language;

	/**
	 * @reference contact
	 * @OneToOne(targetEntity="Model\Contact\Entity\Contact", mappedBy="contactID", canBeNULL=TRUE)
	 * @var \Model\Contact\Entity\Contact
	 */
	protected $contact;

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
	 * @param \Model\Library\Entity\Language $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
	 * @param \Model\Contact\Entity\Contact $contact
	 */
	public function setContact(Contact $contact) {
		$this->contact = $contact;
	}

} 