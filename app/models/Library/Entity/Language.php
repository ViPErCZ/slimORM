<?php
/**
 * User: Martin
 * Date: 10.12.13
 * Time: 13:37
 */

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
	 * @param int $languageID
	 */
	public function setLanguageID($languageID)
	{
		$this->languageID = $languageID;
	}

	/**
	 * @param string $lang
	 */
	public function setLang($lang)
	{
		$this->lang = (string)$lang;
	}

	/**
	 * @return string
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @return int
	 */
	public function getLanguageID()
	{
		return $this->languageID;
	}

} 