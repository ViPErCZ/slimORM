<?php

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
	 * @return int|null
	 */
	public function getAuthorID(): ?int {
		return $this->authorID;
	}

	/**
	 * @param int $authorID
	 */
	public function setAuthorID($authorID): void {
		$this->authorID = (int)$authorID;
	}

	/**
	 * @return Book|null
	 */
	public function getBook(): ?Book {
		return $this->book;
	}

	/**
	 * @param Book $book
	 */
	public function setBook(Book $book): void {
		$this->book = $book;
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
	 * @return Contact|null
	 */
	public function getContact(): ?Contact {
		return $this->contact;
	}

	/**
	 * @param Contact $contact
	 */
	public function setContact(Contact $contact): void {
		$this->contact = $contact;
	}

	/**
	 * @return int|null
	 */
	public function getContactID(): ?int {
		return $this->contactID;
	}

	/**
	 * @param int $contactID
	 */
	public function setContactID($contactID): void {
		$this->contactID = $contactID;
	}

	/**
	 * @return Language|null
	 */
	public function getLanguage(): ?Language {
		return $this->language;
	}

	/**
	 * @param Language $language
	 */
	public function setLanguage(Language $language): void {
		$this->language = $language;
	}

	/**
	 * @return int|null
	 */
	public function getLanguageID(): ?int {
		return $this->languageID;
	}

	/**
	 * @param int $languageID
	 */
	public function setLanguageID($languageID): void {
		$this->languageID = $languageID;
	}
} 