<?php

/**
 * Description of BaseFrameworkTest
 *
 * @author Martin Chudoba
 */
abstract class BaseFrameworkTest extends PHPUnit_Framework_TestCase {
	/** @var \Nette\DI\Container */
	protected $context;

	/**
	 * BaseFrameworkTest constructor.
	 */
	public function __construct() {
		$this->context = System::$context;
	}
}

?>
