<?php

namespace Model\Library\Entity;

use slimORM\Entity\Entity;

/**
 * Class Language
 * @table language
 * @package Model\Library\Entity
 */
class Language extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $languageID;

	/**
	 * @column
	 * @var string
	 */
	protected $lang;

	/**
	 * @return string|null
	 */
	public function getLang(): ?string {
		return $this->lang;
	}

	/**
	 * @param string $lang
	 */
	public function setLang($lang): void {
		$this->lang = (string)$lang;
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