<?php

$phpUnitTest = TRUE; // zajistí, aby se spustil Nette framework (application->run())
require_once __DIR__ . '/../../index.php';

/**
 * Description of SlimORMTest
 *
 * @author Chudoba Martin
 */
class slimORMUnitTest extends BaseDbUnitTestCase {

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_ReplacementDataSet
	 */
	protected function getDataSet() {
		$dataSet = $this->createXMLDataSet(__DIR__ .'/_file/model.xml');
		$replacement = new \PHPUnit\DbUnit\DataSet\ReplacementDataSet($dataSet);
		$replacement->addFullReplacement("###NULL###", null);
		return $replacement;
	}

	// Repository Create
	public function testGetRepository() {
		$repo = new \Model\Library\LibraryRepository($this->emanager);

		$lib1 = $repo->get(1);
		$lib2 = $repo->get(2);

		$this->assertEquals($lib1->getName(), "Library 1");
		$this->assertEquals($lib2->getName(), "Library 2");
		$this->assertEquals($lib1->getLibrarian()->getName(), "Librarian 1");
		$this->assertEquals($lib2->getLibrarian()->getName(), "Librarian 2");

		//magic getter is possible
		$this->assertEquals($lib1->getLibrarian()->getName(), "Librarian 1");
		$this->assertEquals($lib2->getLibrarian()->getName(), "Librarian 2");

		$this->assertInstanceOf('__slimORM__ModelLibraryEntityLibraryEntity', $lib1);
	}

	// Annotations test
	public function testAnnotations() {
		$library = new \Model\Library\Entity\Library();
		$references = $library->getReferences();
		$this->assertEquals($references['books']->property, "books");
	}

	// Select method test
	public function testSelect() {
		$authorRepository = new \Model\Library\AuthorRepository($this->emanager);
		$authors = $authorRepository->read()->select("authorID, name");

		/** @var Author $author */
		foreach ($authors as $author) {
			$this->assertEquals($author->getLanguageID(), null);
		}
	}

	/**
	 *
	 */
	public function testSelectException() {
		$authorRepository = new \Model\Library\AuthorRepository($this->emanager);
		// do not return Exception because primary key automatic added - equal previous test
		$authors = $authorRepository->read()->select("name");

		/** @var Author $author */
		foreach ($authors as $author) {
			$this->assertEquals($author->getLanguageID(), null);
		}
	}

	// Read attachments read
	public function testReadAttachments() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);
		$books = $library->getBooks();
		$attachments = $books->get(1)->getAttachments();
		foreach ($attachments as $attachment) {
			if ($attachment->getAttachmentID() == 1) {
				$this->assertEquals($attachment->getAttachment()->getName(), "CD");
				$this->assertEquals($attachment->getName(), "CD");
			}
		}
	}

	// Read test
	public function testRead() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->read();

		$this->assertEquals($libraries->count("*"), 2);

		$lib1 = $libraries->get(1);
		$lib2 = $libraries->get(2);
		$this->assertEquals($lib1->getName(), "Library 1");
		$this->assertEquals($lib2->getName(), "Library 2");

		$sortLibraries = $libraryRepository->read();
		$sortLibraries->order("libraryID DESC");
		$lib1 = $sortLibraries->get(1);
		$lib2 = $sortLibraries->get(2);
		$this->assertEquals($lib1->getName(), "Library 1");
		$this->assertEquals($lib2->getName(), "Library 2");

		$books = $lib1->getBooks();
		$this->assertEquals($books->get(1)->getName(), "PHP Programing");
		$this->assertEquals($books->get(2)->getName(), "C++ Programing");
		$this->assertEquals($books->get(3)->getName(), "The Road");

		$author = $books->get(1)->getAuthor();
		$this->assertEquals($author->getName(), "Martin Chudoba");
		$this->assertEquals($author->getLanguage()->getLang(), "cz");

		$backBook = $author->getBook();
		$this->assertEquals($backBook->getName(), "PHP Programing");
	}

	// Update test
	public function testUpdate() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->read();

		$lib1 = $libraries->get(1);

		$lib1->setName("Library 1 - updated");
		$libraryRepository->save();

		$this->assertEquals($lib1->getName(), "Library 1 - updated");

		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->getName(), "Library 1 - updated");

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->getName(), "Library 1 - updated");

		$books = $lib1->getBooks();
		$author = $books->get(1)->getAuthor();
		$this->assertEquals($author->getName(), "Martin Chudoba");

		$author->setName("Martin Chudoba - updated");
		$firstBook = $lib1->getBooks()->get(1);
		$bookAttachments = $firstBook->getAttachments()->fetch();
		$attachment = $bookAttachments->getAttachment();
		$attachment->setName("Change CD");

		$newAttachment = new \Model\Library\Entity\Attachment();
		$newAttachment->setEntityManager($this->emanager);
		$newAttachment->setName("ZOO tickets");

		$firstBook->addAttachment($newAttachment);

		$libraryRepository->save();

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$books = $lib1->getBooks();
		$author = $books->get(1)->getAuthor();
		$firstBook = $books->get(1);
		$bookAttachments = $firstBook->getAttachments();
		$attachment = $bookAttachments->fetch();

		$this->assertEquals($author->getName(), "Martin Chudoba - updated");
		$this->assertEquals($bookAttachments->count('*'), 2);
		$this->assertEquals($attachment->getName(), "Change CD");
	}

	// Add book test
	public function testAddBook() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$newBook = new \Model\Library\Entity\Book();
		$newBook->setEntityManager($this->emanager);
		$newBook->setName("Add test book");

		$language = new \Model\Library\Entity\Language();
		$language->setEntityManager($this->emanager);
		$language->setLang("de");

		$newBookAuthor = new \Model\Library\Entity\Author();
		$newBookAuthor->setEntityManager($this->emanager);
		$newBookAuthor->setName("William Pascal");
		$newBookAuthor->setLanguage($language);

		$newBook->setAuthor($newBookAuthor);
		$library->addBooks($newBook);

		$libraryRepository
			->push($library)
			->save();

		$lastInsert = $library->getBooks()->getLastInsertID();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);
		$assertBooks = $assertLibrary->getBooks()->get($lastInsert);

		$this->assertEquals($assertBooks->getName(), "Add test book");
		$this->assertInstanceOf('Model\Library\Entity\Library', $assertBooks->getLibrary());
		$this->assertEquals($assertBooks->getAuthor()->getName(), "William Pascal");
	}

	// Add book with attachments test
	public function testAddBookWithAttachments() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$newBook = new \Model\Library\Entity\Book();
		$newBook->setEntityManager($this->emanager);
		$newBook->setName("Add test book with attachments");
		//$newBook->setLibrary($library); // Volitelné přiřazení knihovny - přiřadí se automaticky po save(...)

		$language = new \Model\Library\Entity\Language();
		$language->setLang("de");

		$attach1 = new \Model\Library\Entity\Attachment();
		$attach1->setName("Attach 1");
		$attach2 = new \Model\Library\Entity\Attachment();
		$attach2->setName("Attach 2");

		$newBookAuthor = new \Model\Library\Entity\Author();
		$newBookAuthor->setName("William Pascal");
		$newBookAuthor->setLanguage($language);

		$newBook->setAuthor($newBookAuthor);
		$newBook->addAttachment($attach1);
		$newBook->addAttachment($attach2);

		$library->addBooks($newBook);

		$libraryRepository
			->push($library)
			->save();

		$lastInsertID = $library->getBooks()->getLastInsertID();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);
		$assertBooks = $assertLibrary->getBooks()->get($lastInsertID);

		$assertAttachments = $assertBooks->getAttachments();

		$this->assertEquals($assertBooks->getName(), "Add test book with attachments");
		$this->assertInstanceOf('Model\Library\Entity\Library', $assertBooks->getLibrary());
		$this->assertEquals($assertBooks->getAuthor()->getName(), "William Pascal");
		$this->assertEquals($assertAttachments->count("*"), 2);
		$this->assertEquals($assertAttachments->fetch()->getName(), "Attach 1");
		$this->assertEquals($assertAttachments->fetch()->getName(), "Attach 2");
	}

	// Add book test with Contact
	public function testAddBookWithContact() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);

		$library = $libraryRepository->get(1);

		$newBook = new \Model\Library\Entity\Book();
		$newBook->setName("Add test book");
		$newBook->setLibrary($library); // Volitelné přiřazení knihovny - přiřadí se automaticky po save(...)

		$language = new \Model\Library\Entity\Language();
		$language->setLang("de");

		$phone = new \Model\Contact\Entity\Phone();
		$phone->setNumber("601 601 601");

		$phone2 = new \Model\Contact\Entity\Phone();
		$phone2->setNumber("608 000 999");

		$rel2 = new \Model\Contact\Entity\Rel2();
		$rel2->setName("sample rel2");

		$rel1 = new \Model\Contact\Entity\Rel1();
		$rel1->setName("sample rel1");
		$rel1->setRel2($rel2);

		$contact = new \Model\Contact\Entity\Contact();
		$contact->setEntityManager($this->emanager);
		$contact->setAddress("Prague 6, Pankrac 501 00");
		$contact->addPhones($phone);
		$contact->addPhones($phone2);
		$contact->setRel1($rel1);

		$newBookAuthor = new \Model\Library\Entity\Author();
		$newBookAuthor->setName("William Pascal");
		$newBookAuthor->setLanguage($language);
		$newBookAuthor->setContact($contact);

		$newBook->setAuthor($newBookAuthor);

		$library->addBooks($newBook);

		$libraryRepository
			->push($library)
			->save();

		$endKey = $library->getBooks()->getLastInsertID();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);

		$lastBook = $assertLibrary->getBooks()->get($endKey);

		$this->assertEquals($lastBook->getName(), "Add test book");
		$this->assertInstanceOf('Model\Library\Entity\Library', $lastBook->getLibrary());
		$this->assertEquals($lastBook->getAuthor()->getName(), "William Pascal");
		$this->assertEquals($lastBook->getAuthor()->getContact()->getAddress(), "Prague 6, Pankrac 501 00");
		$phones = $lastBook->getAuthor()->getContact()->getPhones();


		$this->assertEquals($phones->fetch()->getNumber(), "601 601 601");
		$this->assertEquals($phones->fetch()->getNumber(), "608 000 999");
	}

	// Create test
	public function testCreate() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = new \Model\Library\Entity\Library();
		$library->setEntityManager($this->emanager);
		$library->setName("Library - inserted");

		$book = new \Model\Library\Entity\Book();
		$book->setName("True lies");

		$author = new \Model\Library\Entity\Author();
		$author->setName("Bop Forest");
		$author->setLanguageID(1);

		$author->setBook($book);
		$book->setAuthor($author);
		$library->addBooks($book);

		$repo = $libraryRepository->push($library);
		$repo->save();
		$lastID = $repo->getLastInsertID();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get($lastID);

		$this->assertEquals($assertLibrary->getName(), "Library - inserted");
		foreach ($assertLibrary->getBooks() as $item) {
			$this->assertEquals($item->getName(), "True lies");
			$this->assertEquals($item->getAuthor()->getName(), "Bop Forest");
		}
	}

	// Another repository read test
	public function testAuthorRepositoryRead() {
		$authorRepos = new \Model\Library\AuthorRepository($this->emanager);
		$author = $authorRepos->read()->where("bookID", 1)->fetch();
		$library = $author->getBook()->getLibrary();

		$this->assertEquals($author->getName(), "Martin Chudoba");
		$this->assertEquals($library->getName(), "Library 1");
	}

	// Another repository update test
	public function testAuthorRepositoryUpdate() {
		$authorRepos = new \Model\Library\AuthorRepository($this->emanager);
		$author = $authorRepos->read()->where("bookID", 1)->fetch();

		$author->setName("Martin Chudoba - updated");
		$authorRepos->save();

		$author = $authorRepos->read()->where("bookID", 1)->fetch();
		$this->assertEquals($author->getName(), "Martin Chudoba - updated");
	}

	// Author repository insert test
	public function testContactRepositoryInsert() {
		$contactRepository = new \Model\Contact\ContactRepository($this->emanager);
		$phone = new \Model\Contact\Entity\Phone();
		$phone->setNumber("666 888 999");

		$phone2 = new \Model\Contact\Entity\Phone();
		$phone2->setNumber("111 222 333");

		$rel2 = new \Model\Contact\Entity\Rel2();
		$rel2->setName("sample rel4");

		$rel1 = new \Model\Contact\Entity\Rel1();
		$rel1->setName("sample rel3");
		$rel1->setRel2($rel2);

		$contact = new \Model\Contact\Entity\Contact();
		$contact->setEntityManager($this->emanager);
		$contact->setAddress("Washington DC");
		$contact->addPhones($phone);
		$contact->addPhones($phone2);
		$contact->setRel1($rel1);

		$contactRepository
			->push($contact)
			->save();

		$key = $contactRepository->getLastInsertID();
		$contactRepository->clear();
		$contact = $contactRepository->get($key);
		$this->assertEquals($contact->getAddress(), "Washington DC");
		$this->assertEquals($contact->getRel1()->getName(), "sample rel3");
		$this->assertEquals($contact->getRel1()->getRel2()->getName(), "sample rel4");
		$phones = $contact->getPhones();

		$this->assertEquals($phones->fetch()->getNumber(), "666 888 999");
		$this->assertEquals($phones->fetch()->getNumber(), "111 222 333");
	}

	public function testContactRepositoryUpdate() {
		$contactRepository = new \Model\Contact\ContactRepository($this->emanager);

		$contacts = $contactRepository->read()->fetchAll();
		$keys = array_keys($contacts);
		$endKey = end($keys);
		$contact = $contacts[$endKey];

		$contact->setAddress("Washington DC - updated");
		$phoneRepos = $contact->getPhones();
		if ($phoneRepos) {
			$phoneRepos->fetch()->setNumber("666 888 999 - updated");
			$phoneRepos->fetch()->setNumber("111 222 333 - updated");

			$contact->getRel1()->setName("sample rel3 - updated");
			$contact->getRel1()->getRel2()->setName("sample rel4 - updated");

			$contactRepository->save();

			$data = $contactRepository->read()->fetchAll();
			$keys = array_keys($contacts);
			$endKey = end($keys);
			$contact = $data[$endKey];

			$this->assertEquals($contact->getAddress(), "Washington DC - updated");
			$this->assertEquals($contact->getRel1()->getName(), "sample rel3 - updated");
			$this->assertEquals($contact->getRel1()->getRel2()->getName(), "sample rel4 - updated");

			$phones = $contact->getPhones();

			$this->assertEquals($phoneRepos->fetch()->getNumber(), "666 888 999 - updated");
			$this->assertEquals($phoneRepos->fetch()->getNumber(), "111 222 333 - updated");
		} else {
			$this->assertInstanceOf('__slimORM__ModelPhoneEntityPhoneEntity', $phoneRepos);
		}
	}

	public function testMyselfRepos() {
		$myselfRepos = new \Model\MySelf\MyselfRepository($this->emanager);
		$myselfEnt = new \Model\MySelf\Entity\Myself();
		$myselfChildEnt = new \Model\MySelf\Entity\Myself();

		$myselfEnt->setName("Parent item");
		$myselfChildEnt->setName("Child item");
		$myselfEnt->setChild($myselfChildEnt);

		$myselfRepos
			->push($myselfEnt)
			->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item");

		$data->getChild()->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($data->getName(), "Parent item");
		$child = $data->getChild();
		$this->assertEquals($child->getName(), "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
		$this->database->query("TRUNCATE TABLE phone");
	}

	public function testSetValues() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->read();

		$lib1 = $libraries->get(1);

		$record = array(
			"name"	=> "My slimORM Library",
		);
		$lib1->setValues($record);
		$libraryRepository->save();

		$this->assertEquals($lib1->getName(), "My slimORM Library");

		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->getName(), "My slimORM Library");

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->getName(), "My slimORM Library");
	}

	public function testWherePrimary() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->read()->wherePrimary(array("1"=>1,"2"=>2));

		$this->assertEquals($libraries->count('*'), 2);

		$lib1 = $libraryRepository->fetch();
		$lib2 = $libraryRepository->fetch();

		$this->assertEquals($lib1->getName(), "Library 1");
		$this->assertEquals($lib2->getName(), "Library 2");
	}

	/**
	 * @expectedException slimORM\Exceptions\RepositoryException
	 */
	public function testWhereException() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->where("libraryID", 1);
	}

    //performance test (my test: 59 second, 29.25Mb)
    // slimORM is slower and using more RAM
    // syntax is more intuitive and uses IDE autocomplete
	/*public function testPerformance() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$libraries = $libraryRepository->read();

		$lib1 = $libraries->get(1);

		for ($x = 0; $x < 2000; $x++) {
			$newBook = new \Model\Library\Entity\Book();
			$newBook->setEntityManager($this->emanager);
			$newBook->setName("Add test book");

			$language = new \Model\Library\Entity\Language();
			$language->setEntityManager($this->emanager);
			$language->setLang("de");

			$newBookAuthor = new \Model\Library\Entity\Author();
			$newBookAuthor->setEntityManager($this->emanager);
			$newBookAuthor->setName("William Pascal");
			$newBookAuthor->setLanguage($language);

			$newBook->setAuthor($newBookAuthor);
			$lib1->addBook($newBook);
		}

		$libraryRepository->save();

		$libraryRepository->clear();
		$libraries = $libraryRepository->read();

		$lib1 = $libraries->get(1);
		//$lib1->getBooks()->limit(100); // to performance set the limit!!! - books are not changed
		$lib1->setName("Performance library");
		$libraryRepository->save();
		$this->assertEquals($lib1->getName(), "Performance library");
	}*/

    // performance test - NDB (my test: 44 second, 7.50Mb)
    // simply NDB is faster and using less RAM
    // syntax is more difficult
    /*public function testNdbComparePerformance() {
        $language = $this->database->table("language");
        $author = $this->database->table("author");
        $book = $this->database->table("book");

        $languageID = NULL;
        $bookID = NULL;

        for ($x = 0; $x < 2000; $x++) {
            $data = array(
                "lang" => "de"
            );
            $result = $language->insert($data);
            $languageID = $result->languageID;
        }

        for ($x = 0; $x < 2000; $x++) {
            $data = array(
                "libraryID"     => 1,
                "name"          => "Add test book"
            );
            $result = $book->insert($data);
            $bookID = $result->bookID;
        }

        for ($x = 0; $x < 2000; $x++) {
            $data = array(
                "bookID"        => $bookID,
                "languageID"    => $languageID,
                "name"          => "William Pascal"
            );
            $author->insert($data);
        }

        $libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
        $libraries = $libraryRepository->read();

        $lib1 = $libraries->get(1);
        $lib1->setName("Performance library");
        $libraryRepository->save();
        $this->assertEquals($lib1->getName(), "Performance library");
    }*/
}

?>
