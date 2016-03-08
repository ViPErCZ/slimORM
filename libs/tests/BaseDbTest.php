<?php

use Nette\Environment;
use Nette\Database\Connection;
use Nette\DI\Container;

/**
 * Disables foreign key checks temporarily.
 */
class TruncateOperation extends \PHPUnit_Extensions_Database_Operation_Truncate {
	public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection,
							\PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet) {
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

class InsertOperation extends \PHPUnit_Extensions_Database_Operation_Insert {
	public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection,
							\PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet) {
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

/**
 * Class BaseDbTest
 */
abstract class BaseDbTest extends PHPUnit_Extensions_Database_TestCase {
	/** @var Container */
	protected $context;
	
	/** @var Connection */
	protected $database;

	/** @var \Nette\Caching\Cache */
	protected $cache;

	/** @var \slimORM\EntityManager */
	protected $emanager;

	/** Konstruktor
	 *
	 */
	public function __construct() {
		$this->context = Environment::getContext();
	}
	
	/** Vrací připojení na databázi
	 * 
	 * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function getConnection() {
		$this->database = $this->context->getByType('Nette\Database\Context');
		$this->cache = new \Nette\Caching\Cache($this->context->cacheStorage, 'slimORM');
		$this->emanager = new \slimORM\EntityManager($this->database, $this->cache);
		return $this->createDefaultDBConnection($this->database->getConnection()->getPdo(), $this->context->parameters['database']['dbname']);
	}
	
	/** Abstract metod declaration ************************* */
	/** **************************************************** */

	protected function getTearDownOperation() {
		$cascadeTruncates = true; // If you want cascading truncates, false otherwise. If unsure choose false.

		return new \PHPUnit_Extensions_Database_Operation_Composite(array(
			new TruncateOperation($cascadeTruncates)
		));
	}

	/**
	 * @see PHPUnit_Extensions_Database_TestCase::getSetUpOperation ()
	 */
	protected function getSetUpOperation () {
		$cascadeTruncates = true; // If you want cascading truncates, false otherwise. If unsure choose false.

		return new \PHPUnit_Extensions_Database_Operation_Composite(array(
			new TruncateOperation($cascadeTruncates),
			new InsertOperation()
		));
	}
}
