<?php

$phpUnitTest = TRUE; // zajistí, aby se spustil Nette framework (application->run())
require_once __DIR__ . '/../../index.php';

/**
 * Created by PhpStorm.
 * User: viper
 * Date: 2.3.16
 * Time: 16:01
 */
class GeneratedClassUnitTest extends BaseDbUnitTestCase {

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_ReplacementDataSet
	 */
	protected function getDataSet() {
		$dataSet = $this->createXMLDataSet(__DIR__ .'/_file/model.xml');
		$replacement = new \PHPUnit\DbUnit\DataSet\ReplacementDataSet($dataSet);
		$replacement->addFullReplacement("###NULL###", null);
		return $replacement;
	}

	public function testReadBook() {
		$bookRepository = new \Model\Library\BookRepository($this->emanager);
		$book = $bookRepository->get(1);
		$library = $book->getLibrary();
		$author = $book->getAuthor();

		$this->assertEquals("PHP Programing", $book->getName());
		$this->assertEquals("Library 1", $library->getName());
		$this->assertEquals("Martin Chudoba", $author->getName());
	}

	public function testRecursiveAdd() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$book = new \Model\Library\Entity\Book();
		$book->setName("Tester add Name");

		$language = new \Model\Library\Entity\Language();
		$language->setLang("fr");

		$author = new \Model\Library\Entity\Author();
		$author->setName("ViPErCZ");
		$author->setLanguage($language);

		$book->setAuthor($author);

		$library->addBooks($book);

		$libraryRepository->save();

		$bookRepository = new \Model\Library\BookRepository($this->emanager);
		$bookTest = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest = $bookTest->getAuthor();
		$languageTest = $authorTest->getLanguage();

		$this->assertEquals("Tester add Name", $bookTest->getName());
		$this->assertEquals("ViPErCZ", $authorTest->getName());
		$this->assertEquals("fr", $languageTest->getLang());
	}

	public function testRecursiveUpdate() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$book = new \Model\Library\Entity\Book();
		$book->setName("Tester add Name");

		$language = new \Model\Library\Entity\Language();
		$language->setLang("fr");

		$author = new \Model\Library\Entity\Author();
		$author->setName("ViPErCZ");
		$author->setLanguage($language);

		$book->setAuthor($author);

		$library->addBooks($book);

		$libraryRepository->save();

		$bookRepository = new \Model\Library\BookRepository($this->emanager);
		$bookTest = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest = $bookTest->getAuthor();
		$languageTest = $authorTest->getLanguage();

		$languageTest->setLang("uk");

		$libraryRepository->save();

		$bookTest2 = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest2 = $bookTest2->getAuthor();
		$languageTest2 = $authorTest2->getLanguage();

		$this->assertEquals("uk", $languageTest2->getLang());
	}

	/**
	 * @expectedException slimORM\Exceptions\RepositoryException
	 */
	public function testWhereException() {
		$bookRepository = new \Model\Library\BookRepository($this->emanager);
		$bookRepository->where("libraryID", 1);
	}

	public function testWherePrimary() {
		$bookRepository = new \Model\Library\BookRepository($this->emanager);
		$book = $bookRepository->read()->wherePrimary(1)->fetch();

		$this->assertEquals("PHP Programing", $book->getName());
	}

	public function testMyselfRepos() {
		$myselfRepos = new \Model\MySelf\MyselfRepository($this->emanager);
		$myselfEnt = new \Model\MySelf\Entity\Myself();
		$myselfChildEnt = new \Model\MySelf\Entity\Myself();

		$myselfEnt->setName("Parent item");
		$myselfChildEnt->setName("Child item");
		$myselfEnt->setChild($myselfChildEnt);

		$result = $myselfRepos
			->push($myselfEnt)
			->save();

		$data = $myselfRepos->read()->where("childID IS NULL")->fetch();
		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($result, true);
		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item");

		$child->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
	}

	public function testMyselfRepos2() {
		$myselfRepos = new \Model\MySelf\MyselfRepository($this->emanager);
		$myselfEnt = new \Model\MySelf\Entity\Myself();
		$myselfChildEnt = new \Model\MySelf\Entity\Myself();

		$myselfEnt->setName("Parent item 2");
		$myselfChildEnt->setName("Child item 2");
		$myselfEnt->setChild($myselfChildEnt);

		$result = $myselfRepos
			->push($myselfEnt)
			->save();

		$data = $myselfRepos->read()->where("childID", 1)->fetch();

		$this->assertEquals($result, true);
		$this->assertEquals($data->getName(), "Parent item 2");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item 2");

		$child->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID", 1)->fetch();

		$this->assertEquals($data->getName(), "Parent item 2");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
	}
}