<?php

use Nette\Environment;
use Nette\Database\Connection;
use Nette\DI\Container;

/**
 * Description of BaseDbTest
 *
 * @author Martin Chudoba
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 *   
 */
abstract class BaseDbTest extends PHPUnit_Extensions_Database_TestCase {
	/** @var Container */
	protected $context;
	
	/** @var Connection */
	protected $database;
	
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
		$this->database = $this->context->database;
		return $this->createDefaultDBConnection($this->database->getConnection()->getPdo(), $this->context->parameters['database']['dbname']);
	}
	
	/** Abstract metod declaration ************************* */
	/** **************************************************** */
	
	protected function getSetUpOperation() {
		$this->getConnection()->getConnection()->query("SET foreign_key_checks = 0;")->execute();
		$result = $this->getOperations()->INSERT();
		$this->getConnection()->getConnection()->query("SET foreign_key_checks = 1")->execute();
		return $result;
	}

	protected function getTearDownOperation() {
		$this->getConnection()->getConnection()->query("SET foreign_key_checks = 0;")->execute();
		$result = $this->getOperations()->DELETE_ALL();
		$this->getConnection()->getConnection()->query("SET foreign_key_checks = 1")->execute();
		
		return $result;
		//return PHPUnit_Extensions_Database_Operation_Factory::NONE();
	}
}
