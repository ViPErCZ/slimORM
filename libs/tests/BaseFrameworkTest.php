<?php

/**
 * Description of BaseFrameworkTest
 *
 * @author Martin Chudoba
 */
abstract class BaseFrameworkTest extends \PHPUnit\Framework\TestCase {

	/** @var \Nette\DI\Container */
	protected $context;

	/**
	 * BaseFrameworkTest constructor.
	 * @param null $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);

		$this->context = System::$context;
	}
}

?>
