<?php

$phpUnitTest = TRUE; // zajistí, aby se spustil Nette framework (application->run())
require_once dirname(__FILE__) . "/../../index.php";

/**
 * Description of SlimORMTest
 *
 * @author Chudoba Martin
 */
class SlimORMTest extends BaseDbTest {

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_ReplacementDataSet
	 */
	protected function getDataSet() {
		$dataSet = $this->createXMLDataSet(dirname(__FILE__).'/_file/model.xml');
		$replacement = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet);
		$replacement->addFullReplacement("###NULL###", null);
		return $replacement;
	}
	
	/** Read test
	 * 
	 */
	public function testRead() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->database);
		$libraries = $libraryRepository->read();

		$this->assertEquals($libraries->count("*"), 2);

		$lib1 = $libraries->get(1);
		$lib2 = $libraries->get(2);
		$this->assertEquals($lib1->name, "Library 1");
		$this->assertEquals($lib2->name, "Library 2");

		$sortLibraries = $libraryRepository->read()->order("libraryID DESC");
		$lib1 = $sortLibraries->get(1);
		$lib2 = $sortLibraries->get(2);
		$this->assertEquals($lib1->name, "Library 1");
		$this->assertEquals($lib2->name, "Library 2");

		$books = $lib1->getBooks();
		$this->assertEquals($books[1]->name, "PHP Programing");
		$this->assertEquals($books[2]->name, "C++ Programing");
		$this->assertEquals($books[3]->name, "The Road");

		$author = $books[1]->getAuthor();
		$this->assertEquals($author->name, "Martin Chudoba");

		$backBook = $author->getBook();
		$this->assertEquals($backBook->name, "PHP Programing");
	}

	/** Update test
	 *
	 */
	public function testUpdate() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->database);
		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);

		$lib1->name = "Library 1 - updated";
		$libraryRepository->save();

		$this->assertEquals($lib1->name, "Library 1 - updated");

		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->name, "Library 1 - updated");

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->name, "Library 1 - updated");

		$books = $lib1->getBooks();
		$author = $books[1]->getAuthor();
		$this->assertEquals($author->name, "Martin Chudoba");

		$author->name = "Martin Chudoba - updated";
		$libraryRepository->save();

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$books = $lib1->getBooks();
		$author = $books[1]->getAuthor();

		$this->assertEquals($author->name, "Martin Chudoba - updated");
	}

	/** Inserts test
	 *
	 */
	public function testInserts() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->database);
		$libraries = $libraryRepository->read();
	}
}

?>
