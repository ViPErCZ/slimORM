<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 13:22
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

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
	 * @reference library
	 * @var Library
	 */
	protected $library;

	/**
	 * @param int $librarianID
	 */
	public function setLibrarianID($librarianID)
	{
		$this->librarianID = $librarianID;
	}

	/**
	 * @param Library $library
	 */
	public function setLibrary(Library $library)
	{
		$this->library = $library;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
	}

} 