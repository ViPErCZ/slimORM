<?php

defined('__PHPUNIT_PHAR__');

use Nette\Environment;
use Nette\Database\Connection;
use Nette\DI\Container;

/**
 * Disables foreign key checks temporarily.
 */
class TruncateOperation extends \PHPUnit\DbUnit\Operation\Truncate {
	public function execute(\PHPUnit\DbUnit\Database\Connection $connection, \PHPUnit\DbUnit\DataSet\IDataSet $dataSet) {
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

class InsertOperation extends \PHPUnit\DbUnit\Operation\Insert {
	public function execute(\PHPUnit\DbUnit\Database\Connection $connection, \PHPUnit\DbUnit\DataSet\IDataSet $dataSet) {
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

/**
 * Class BaseDbTest
 */
abstract class BaseDbUnitTestCase extends \PHPUnit\Framework\TestCase {

	use \PHPUnit\DbUnit\TestCaseTrait;

	/** @var Container */
	protected $context;
	
	/** @var Connection */
	protected $database;

	/** @var \Nette\Caching\Cache */
	protected $cache;

	/** @var \slimORM\EntityManager */
	protected $emanager;

	/**
	 * BaseDbTest constructor.
	 * @param null $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);

		$this->context = System::$context;
	}


	/** Vrací připojení na databázi
	 * 
	 * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function getConnection() {
		$this->database = $this->context->getByType('Nette\Database\Context');
		$this->cache = new \Nette\Caching\Cache($this->context->getByType('Nette\Caching\IStorage'), 'slimORM');
		$this->emanager = new \slimORM\EntityManager($this->database, $this->cache);

		return $this->createDefaultDBConnection($this->database->getConnection()->getPdo(), null);
	}

	/** Abstract metod declaration ************************* */
	/** **************************************************** */

	protected function getTearDownOperation() {
		$cascadeTruncates = true; // If you want cascading truncates, false otherwise. If unsure choose false.

		return new \PHPUnit\DbUnit\Operation\Composite(array(
			new TruncateOperation($cascadeTruncates)
		));
	}

	/**
	 * @see PHPUnit_Extensions_Database_TestCase::getSetUpOperation ()
	 */
	protected function getSetUpOperation () {
		$cascadeTruncates = true; // If you want cascading truncates, false otherwise. If unsure choose false.

		return new \PHPUnit\DbUnit\Operation\Composite(array(
			new TruncateOperation($cascadeTruncates),
			new InsertOperation()
		));
	}
}
