<?php

namespace Model\Library\Entity;

use slimORM\Entity\Entity;

/**
 * Class Librarian
 * @table librarian
 * @package Model\Library\Entity
 */
class Librarian extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $librarianID;

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
	 * @reference library
	 * @OneToOne(targetEntity="Model\Library\Entity\Library", mappedBy="libraryID")
	 * @var Library
	 */
	protected $library;

	/**
	 * @param int $librarianID
	 */
	public function setLibrarianID($librarianID): void {
		$this->librarianID = $librarianID;
	}

	/**
	 * @param Library|null $library
	 */
	public function setLibrary(Library $library = null): void {
		$this->library = $library;
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

} 