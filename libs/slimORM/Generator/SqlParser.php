<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 20.12.15
 * Time: 17:37
 */

namespace slimORM\Generator;

use slimORM\Generator\Sql\Reference;
use slimORM\Generator\Sql\Table;

/**
 * Class SqlParser
 * @package slimORM\Generator
 */
class SqlParser extends Parser{

	/** @var Table[] */
	protected $tables;

	/**
	 * SqlParser constructor.
	 * @param string $path
	 */
	public function __construct($path) {
		parent::__construct($path);

		$this->tables = array();
	}

	/**
	 * @return Sql\Table[]
	 */
	public function getTables() {
		if (count($this->tables) == 0) {
			$this->parse();
		}
		return $this->tables;
	}

	/**
	 *
	 */
	public function parse() {
		$this->openFile();

		$delimiter = ';';
		$sql = '';

		while (!feof($this->handle)) {
			$s = rtrim(fgets($this->handle));
			if (!strncasecmp($s, 'DELIMITER ', 10)) {
				$delimiter = substr($s, 10);

			} elseif (substr($s, -strlen($delimiter)) === $delimiter) {
				$sql .= substr($s, 0, -strlen($delimiter));
				$this->parseSql($sql);

				$sql = '';
			} else {
				$sql .= $s . "\n";
			}
		}

		$this->generatedRelated();

		$this->close();
	}

	/**
	 *
	 */
	protected function generatedRelated() {
		if (count($this->tables) > 0) {
			foreach ($this->getTables() as $table) {
				$related = $this->findRelated($table->getName());
				foreach ($related as $rel) {
					$this->tables[$table->getName()]->addRelated($rel);
				}
			}
		}
	}

	/**
	 * @param $sql
	 * @throws SqlParserException
	 */
	protected function parseSql($sql) {
		if ($sql != "" && preg_match('/^.*CREATE TABLE/m', $sql) != false) {
			try {
				$tableName = $this->getTableName($sql);
				$columns = $this->getColumnsName($sql);
				$references = $this->getReferences($sql);

				$this->tables[$tableName] = new Table($tableName, $columns);

				foreach ($references as $reference) {
					$this->tables[$tableName]
						->addReference(new Reference($reference['key'], $reference["columnName"], $reference['table']));
				}

			} catch (SqlParserException $e) {
				throw new SqlParserException($e->getMessage());
			}
		}
	}

	/**
	 * @param $sql
	 * @return mixed
	 * @throws SqlParserException
	 */
	protected function getTableName($sql) {
		preg_match('/^.*CREATE TABLE.*`{1}\w+`{1}\.?`?\w*`?/m', $sql, $matches);

		if (count($matches) == 1) {
			preg_match('/`{1}\w+`{1}\.?`?\w*`?/m', $matches[0], $tableName);

			if (count($tableName) == 1) {
				$tableName = str_replace("`", "", $tableName[0]);
				if (strpos($tableName, ".") !== false) {
					$tableName = explode(".", $tableName)[1];
				}
				return $tableName;
			} else {
				throw new SqlParserException("Syntax error. Table name not found.");
			}
		} else {
			throw new SqlParserException("Syntax error. CREATE TABLE not found.");
		}
	}

	/**
	 * @param $sql
	 * @return array
	 */
	protected function getColumnsName($sql) {
		$columns = array();
		preg_match('/\(.+\)/sm', $sql, $matches);

		if (count($matches) == 1) {
			$explode = explode(PHP_EOL, $matches[0]);
			foreach ($explode as $line) {
				preg_match('/^`{1}\w+`{1}/', trim($line), $column);

				if (count($column) == 1) {
					preg_match('/`{1} [a-zA-Z0-9_()]+ {1}/', trim($line), $type);
					$name = str_replace("`", "", $column[0]);
					$columns[$name] = array(
						"name"		=> $name,
						"type"		=> trim(str_replace("` ", "", $type[0])),
						"primary"	=> false,
						"null"		=> strpos(trim($line), "NOT NULL") !== false ? false : true
					);

					preg_match('/PRIMARY KEY \(`\w+`\)/', $sql, $primary);
					if (count($primary) == 1) {
						$subject = $primary[0];
						$key = preg_replace_callback('|(PRIMARY KEY )(\(`)(\w+)(`\))|', function($matches) {
							return $matches[3];
						}, $subject);
						if ($name == $key) {
							$columns[$name]['primary'] = true;
						}
					}
				}
			}
		}

		return $columns;
	}

	/**
	 * @param $sql
	 * @return array
	 */
	protected function getReferences($sql) {
		$references = array();
		preg_match_all('/REFERENCES [a-zA-Z0-9_`. (]+\)/m', $sql, $matches);

		if (count($matches) == 1) {
			foreach ($matches[0] as $index => $match) {
				$explode = explode(" ", trim(str_replace("REFERENCES", "", $match)));

				if (count($explode) == 2) {
					$names = explode(".", $explode[0]);
					$table = null;

					if (count($names) == 2) {
						$table = trim(str_replace("`", "", $names[1]));
					} else {
						$table = trim(str_replace("`", "", $explode[0]));
					}
					preg_match('/\w+/', $explode[1], $key);

					if ($table && count($key) == 1) {
						$foreignKey = $this->getForeignKey($sql, $index);
						if ($foreignKey && $foreignKey == $key[0] || $foreignKey === null) {
							$references[] = array(
								"key" 			=> $key[0],
								"columnName" 	=> $key[0],
								"table" 		=> $table
							);
						} else if ($foreignKey && $foreignKey != $key[0]) {
							$references[] = array(
								"key" 			=> $foreignKey,
								"columnName" 	=> $key[0],
								"table" 		=> $table
							);
						}
					}
				}
			}
		}

		return $references;
	}

	/**
	 * @param $sql
	 * @param $index
	 * @return mixed|null
	 */
	protected function getForeignKey($sql, $index) {
		preg_match_all('/FOREIGN KEY [a-zA-Z0-9_`. ()]+/m', $sql, $matches);

		if (count($matches) == 1) {
			return str_replace("`)", "" , trim(str_replace("FOREIGN KEY (`", "", $matches[0][$index])));
		} else {
			return null;
		}
	}

	/**
	 * @param string $tableName
	 * @return array
	 */
	public function findRelated($tableName) {
		$related = array();

		if (array_key_exists($tableName, $this->getTables())) {
			$tableOfName = $this->getTables()[$tableName];
			$primaryKey = $tableOfName->getPrimaryKey();
			if ($primaryKey) {
				foreach ($this->getTables() as $table) {
					$references = $table->getReferences();

					foreach ($references as $reference) {
						if ($reference->getColumnName() == $primaryKey) {
							$related[$table->getName()] = new Reference($reference->getKey(), $primaryKey, $table->getName());
						}
					}
				}
				return $related;
			} else {
				return $related;
			}
		} else {
			return $related;
		}
	}
}