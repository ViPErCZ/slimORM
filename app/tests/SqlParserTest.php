<?php

$phpUnitTest = TRUE; // zajistí, aby se spustil Nette framework (application->run())
require_once dirname(__FILE__) . "/../../index.php";

/**
 * Created by PhpStorm.
 * User: viper
 * Date: 7.1.16
 * Time: 9:28
 */
class SqlParserTest extends BaseFrameworkTest {

	public function testParse() {
		$sqlParser = new \slimORM\Generator\SqlParser(WWW_DIR . "/navrhy/slimorm.sql");
		$tables = $sqlParser->getTables();

		$this->assertEquals(count($tables), 12);
		$this->assertInstanceOf('slimORM\Generator\Sql\Table', $tables['library']);
		$this->assertEquals($tables['library']->getName(), "library");

		$columns = $tables['library']->getColumns();
		$this->assertInstanceOf('slimORM\Generator\Sql\Column', $columns['libraryID']);

		$references = $tables['library']->getReferences();
		$this->assertEquals($references, array());

		$references = $tables['book']->getReferences();
		$this->assertInstanceOf('slimORM\Generator\Sql\Reference', $references['libraryID']);
		$this->assertEquals($references['libraryID']->getTable(), "library");
		$this->assertEquals($columns['libraryID']->isPrimary(), true);
		$this->assertEquals($columns['name']->isPrimary(), false);

		$related = $sqlParser->findRelated("library");

		$this->assertEquals(count($related), 2);
		$this->assertEquals(isset($related['book']), true);
		$this->assertEquals(isset($related['librarian']), true);

		@mkdir(APP_DIR . "/generated/");
		@mkdir(APP_DIR . "/generated/Base");

		foreach ($tables as $table) {
			$entityGenerator = new slimORM\Generator\EntityGenerator(APP_DIR . "/generated/Base/Entity", $table);
			$entityGenerator->generate();

			$repositoryGenerator = new \slimORM\Generator\RepositoryGenerator(APP_DIR . "/generated/Base/", $table);
			$repositoryGenerator->generate();
		}
	}
}