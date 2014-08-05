<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:21
 */

namespace Model\Library\Entity;


use Model\Contact\Entity\Contact;
use slimORM\Entity\Entity;

/**
 * Class Author
 * @table author
 * @package Model\Library\Entity
 */
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
	 * @param int $bookID
	 */
	public function setBookID($bookID)
	{
		$this->bookID = $bookID;
	}

	/**
	 * @param int $contactID
	 */
	public function setContactID($contactID)
	{
		$this->contactID = $contactID;
	}

	/**
	 * @param int $languageID
	 */
	public function setLanguageID($languageID)
	{
		$this->languageID = $languageID;
	}

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

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getAuthorID()
	{
		return $this->authorID;
	}

	/**
	 * @return \Model\Library\Entity\Book
	 */
	public function getBook()
	{
		return $this->book;
	}

	/**
	 * @return int
	 */
	public function getBookID()
	{
		return $this->bookID;
	}

	/**
	 * @return \Model\Contact\Entity\Contact
	 */
	public function getContact()
	{
		return $this->contact;
	}

	/**
	 * @return int
	 */
	public function getContactID()
	{
		return $this->contactID;
	}

	/**
	 * @return \Model\Library\Entity\Language
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return int
	 */
	public function getLanguageID()
	{
		return $this->languageID;
	}
} 