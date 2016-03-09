<?php

$phpUnitTest = TRUE; // zajistí, aby se spustil Nette framework (application->run())
require_once dirname(__FILE__) . "/../../index.php";

/**
 * Created by PhpStorm.
 * User: viper
 * Date: 2.3.16
 * Time: 16:01
 */
class GeneratedClassTest extends BaseDbTest {
	/**
	 * @return PHPUnit_Extensions_Database_DataSet_ReplacementDataSet
	 */
	protected function getDataSet() {
		$dataSet = $this->createXMLDataSet(dirname(__FILE__).'/_file/model.xml');
		$replacement = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet);
		$replacement->addFullReplacement("###NULL###", null);
		return $replacement;
	}

	public function testReadBook() {
		$bookRepository = new BookRepository($this->emanager);
		$book = $bookRepository->get(1);
		$library = $book->getLibrary();
		$author = $book->getAuthor()->read()->fetch();

		$this->assertEquals("PHP Programing", $book->getName());
		$this->assertEquals("Library 1", $library->getName());
		$this->assertEquals("Martin Chudoba", $author->getName());
	}

	public function testRecursiveAdd() {
		$libraryRepository = new LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$book = new Book();
		$book->setName("Tester add Name");

		$language = new Language();
		$language->setLang("fr");

		$author = new Author();
		$author->setName("ViPErCZ");
		$author->setLanguage($language);

		$book->addAuthor($author);

		$library->addBook($book);

		$libraryRepository->save();

		$bookRepository = new BookRepository($this->emanager);
		$bookTest = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest = $bookTest->getAuthor()->fetch(); //do not use read !!!
		$languageTest = $authorTest->getLanguage();

		$this->assertEquals("Tester add Name", $bookTest->getName());
		$this->assertEquals("ViPErCZ", $authorTest->getName());
		$this->assertEquals("fr", $languageTest->getLang());
	}

	public function testRecursiveUpdate() {
		$libraryRepository = new LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$book = new Book();
		$book->setName("Tester add Name");

		$language = new Language();
		$language->setLang("fr");

		$author = new Author();
		$author->setName("ViPErCZ");
		$author->setLanguage($language);

		$book->addAuthor($author);

		$library->addBook($book);

		$libraryRepository->save();

		$bookRepository = new BookRepository($this->emanager);
		$bookTest = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest = $bookTest->getAuthor()->get(3); //do not use read !!!
		$languageTest = $authorTest->getLanguage();

		$languageTest->setLang("uk");

		$libraryRepository->save();

		$bookTest2 = $bookRepository->get($book->getBookID());
		/** @var Author $authorTest */
		$authorTest2 = $bookTest2->getAuthor()->get(3); //do not use read !!!
		$languageTest2 = $authorTest2->getLanguage();

		$this->assertEquals("uk", $languageTest2->getLang());
	}

	/**
	 * @expectedException slimORM\Exceptions\RepositoryException
	 */
	public function testWhereException() {
		$bookRepository = new BookRepository($this->emanager);
		$bookRepository->where("libraryID", 1);
	}

	public function testWherePrimary() {
		$bookRepository = new BookRepository($this->emanager);
		$book = $bookRepository->read()->wherePrimary(1)->fetch();

		$this->assertEquals("PHP Programing", $book->getName());
	}

	public function testMyselfRepos() {
		$myselfRepos = new MyselfRepository($this->emanager);
		$myselfEnt = new Myself();
		$myselfChildEnt = new Myself();

		$myselfEnt->setName("Parent item");
		$myselfChildEnt->setName("Child item");
		$myselfEnt->setMyself($myselfChildEnt);

		$result = $myselfRepos
			->push($myselfEnt)
			->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($result, true);
		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getMyself();
		$this->assertEquals($child->getName(), "Child item");

		$child->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getMyself();
		$this->assertEquals($child->getName(), "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
	}

	public function testMyselfRepos2() {
		$myselfRepos = new MyselfRepository($this->emanager);
		$myselfEnt = new Myself();
		$myselfChildEnt = new Myself();

		$myselfEnt->setName("Parent item 2");
		$myselfChildEnt->setName("Child item 2");
		$myselfEnt->addChild($myselfChildEnt);

		$result = $myselfRepos
			->push($myselfEnt)
			->save();

		$data = $myselfRepos->read()->where("childID", null)->fetch();

		$this->assertEquals($result, true);
		$this->assertEquals($data->getName(), "Parent item 2");
		$child = $data->getChild()->fetch();
		$this->assertEquals($child->getName(), "Child item 2");

		$child->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID", null)->fetch();

		$this->assertEquals($data->getName(), "Parent item 2");
		$child = $data->getChild()->fetch();
		$this->assertEquals($child->getName(), "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
	}
}