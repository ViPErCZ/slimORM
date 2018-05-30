<?php

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