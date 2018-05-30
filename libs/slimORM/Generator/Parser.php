<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 7.1.16
 * Time: 11:33
 */

namespace slimORM\Generator;
use Nette\FileNotFoundException;

/**
 * Class Parser
 * @package slimORM\Generator
 */
abstract class Parser {

	/** @var string */
	protected $path;

	/** @var resource */
	protected $handle;

	/**
	 * Parser constructor.
	 * @param string $path
	 */
	public function __construct($path) {
		@set_time_limit(0); // @ function may be disabled
		$this->path = $path;
		$this->handle = null;
	}

	/**
	 *
	 */
	public function __destruct() {
		// TODO: Implement __destruct() method.
		$this->close();
	}

	/**
	 *
	 */
	protected function openFile() {
		$this->handle = @fopen($this->path, 'r'); // @ is escalated to exception
		if (!$this->handle) {
			throw new FileNotFoundException("Cannot open file '$this->path'.");
		}
	}

	/**
	 *
	 */
	protected function close() {
		if ($this->handle) {
			fclose($this->handle);
			$this->handle = null;
		}
	}

	/** Abstract Functions */
	abstract public function parse();
}