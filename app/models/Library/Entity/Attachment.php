<?php
/**
 * User: Martin
 * Date: 11.12.13
 * Time: 9:09
 */

namespace Model\Library\Entity;


use slimORM\Entity\Entity;

/**
 * Class Attachment
 * @table attachment
 * @package Model\Library\Entity
 */
class Attachment extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $attachmentID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = (string)$name;
	}

	/**
	 * @return int
	 */
	public function getAttachmentID()
	{
		return $this->attachmentID;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

} 