<?php

/**
 * Description of BaseFrameworkTest
 *
 * @author Martin Chudoba
 */
abstract class BaseFrameworkTest extends PHPUnit_Framework_TestCase {
	/** @var \SystemContainer|Nette\DI\Container */
	protected $context;
	
	/** Konstruktor
	 * 
	 */
	public function __construct() {
		$this->context = Nette\Environment::getContext();
	}
}

?>
