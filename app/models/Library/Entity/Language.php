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
	 * @param string $lang
	 */
	public function setLang($lang)
	{
		$this->lang = (string)$lang;
	}

} 