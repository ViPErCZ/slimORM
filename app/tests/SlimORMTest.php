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
		$this->assertEquals($lib2->getLibrarian()->name, "Librarian 2");

		$this->assertInstanceOf('__slimORM__ModelLibraryEntityLibraryEntity', $lib1);
	}

	// Annotations test
	public function testAnnotations() {
		$library = new \Model\Library\Entity\Library();
		$references = $library->getReferences();
		$this->assertEquals($references['books']->property, "books");
	}

	// Read attachments read
	public function testReadAttachments() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);
		$books = $library->getBooks();
		$attachments = $books[1]->getAttachments();
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
		$this->assertEquals($books[1]->getName(), "PHP Programing");
		$this->assertEquals($books[2]->getName(), "C++ Programing");
		$this->assertEquals($books[3]->getName(), "The Road");

		$author = $books[1]->getAuthor();
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
		$this->assertEquals($lib1->name, "Library 1 - updated");

		$libraries = $libraryRepository->read();
		$lib1 = $libraries->get(1);
		$this->assertEquals($lib1->name, "Library 1 - updated");

		$books = $lib1->books;
		if (is_array($books)) {
			$author = $books[1]->author;
			$this->assertEquals($author->name, "Martin Chudoba");

			$author->name = "Martin Chudoba - updated";
			$this->assertEquals($lib1->books[1]->author->name, "Martin Chudoba - updated");

			$firstBook = $lib1->books[1];
			$bookAttachments = $firstBook->attachments;
			current($bookAttachments)->attachment->name = "Change CD";

			$newAttachment = new \Model\Library\Entity\Attachment();
			$newAttachment->setName("ZOO tickets");

			$firstBook->addAttachment($newAttachment);

			$libraryRepository->save();

			$libraries = $libraryRepository->read();
			$lib1 = $libraries->get(1);
			$books = $lib1->books;
			$author = $books[1]->author;
			$firstBook = $books[1];
			$bookAttachments = $firstBook->attachments;

			$this->assertEquals($author->name, "Martin Chudoba - updated");
			$this->assertEquals(count($bookAttachments), 2);
			$this->assertEquals(current($bookAttachments)->name, "Change CD");
		}
	}

	// Add book test
	public function testAddBook() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$newBook = new \Model\Library\Entity\Book();
		$newBook->setName("Add test book");
		//$newBook->setLibrary($library); // Volitelné přiřazení knihovny - přiřadí se automaticky po save(...)

		$language = new \Model\Library\Entity\Language();
		$language->setLang("de");

		$newBookAuthor = new \Model\Library\Entity\Author();
		$newBookAuthor->setName("William Pascal");
		$newBookAuthor->setLanguage($language);

		$newBook->setAuthor($newBookAuthor);

		$library->addBook($newBook);

		$libraryRepository
			->push($library)
			->save();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);
		$assertBooks = $assertLibrary->books;

		$lastBook = end($assertBooks);

		$this->assertEquals($lastBook->name, "Add test book");
		$this->assertInstanceOf('Model\Library\Entity\Library', $lastBook->library);
		$this->assertEquals($lastBook->author->name, "William Pascal");
	}

	// Add book with attachments test
	public function testAddBookWithAttachments() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = $libraryRepository->get(1);

		$newBook = new \Model\Library\Entity\Book();
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

		$library->addBook($newBook);

		$libraryRepository
			->push($library)
			->save();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);
		$assertBooks = $assertLibrary->books;

		$lastBook = end($assertBooks);
		$assertAttachments = $lastBook->attachments;

		$this->assertEquals($lastBook->name, "Add test book with attachments");
		$this->assertInstanceOf('Model\Library\Entity\Library', $lastBook->library);
		$this->assertEquals($lastBook->author->name, "William Pascal");
		$this->assertEquals(count($assertAttachments), 2);
		$this->assertEquals(current($assertAttachments)->name, "Attach 1");
		$this->assertEquals(end($assertAttachments)->name, "Attach 2");
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
		$contact->setAddress("Prague 6, Pankrac 501 00");
		$contact->addPhone($phone);
		$contact->addPhone($phone2);
		$contact->setRel1($rel1);

		$newBookAuthor = new \Model\Library\Entity\Author();
		$newBookAuthor->setName("William Pascal");
		$newBookAuthor->setLanguage($language);
		$newBookAuthor->setContact($contact);

		$newBook->setAuthor($newBookAuthor);

		$library->addBook($newBook);

		$libraryRepository
			->push($library)
			->save();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get(1);
		$assertBooks = $assertLibrary->books;

		$lastBook = end($assertBooks);

		$this->assertEquals($lastBook->name, "Add test book");
		$this->assertInstanceOf('Model\Library\Entity\Library', $lastBook->library);
		$this->assertEquals($lastBook->author->name, "William Pascal");
		$this->assertEquals($lastBook->author->contact->address, "Prague 6, Pankrac 501 00");
		$phones = $lastBook->author->contact->phones;
		$this->assertEquals(current($phones)->number, "601 601 601");
		$this->assertEquals(end($phones)->number, "608 000 999");
	}

	// Create test
	public function testCreate() {
		$libraryRepository = new \Model\Library\LibraryRepository($this->emanager);
		$library = new \Model\Library\Entity\Library();
		$library->setName("Library - inserted");

		$book = new \Model\Library\Entity\Book();
		$book->setName("True lies");

		$author = new \Model\Library\Entity\Author();
		$author->setName("Bop Forest");
		$author->languageID = 1;

		$author->setBook($book);
		$book->setAuthor($author);
		$library->addBook($book);

		$repo = $libraryRepository->push($library);
		$repo->save();
		$lastID = $repo->getLastInsertID();

		$assertRepository = new \Model\Library\LibraryRepository($this->emanager);
		$assertLibrary = $assertRepository->get($lastID);

		$this->assertEquals($assertLibrary->name, "Library - inserted");
		foreach ($assertLibrary->getBooks() as $item) {
			$this->assertEquals($item->getName(), "True lies");
			$this->assertEquals($item->author->name, "Bop Forest");
		}
	}

	// Another repository read test
	public function testAuthorRepositoryRead() {
		$authorRepos = new \Model\Library\AuthorRepository($this->emanager);
		$author = $authorRepos->read()->where("bookID", 1)->fetch();
		$library = $author->book->library;

		$this->assertEquals($author->name, "Martin Chudoba");
		$this->assertEquals($library->name, "Library 1");
	}

	// Another repository update test
	public function testAuthorRepositoryUpdate() {
		$authorRepos = new \Model\Library\AuthorRepository($this->emanager);
		$author = $authorRepos->read()->where("bookID", 1)->fetch();

		$author->name = "Martin Chudoba - updated";
		$authorRepos->save();

		$author = $authorRepos->read()->where("bookID", 1)->fetch();
		$this->assertEquals($author->name, "Martin Chudoba - updated");
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
		$contact->setAddress("Washington DC");
		$contact->addPhone($phone);
		$contact->addPhone($phone2);
		$contact->setRel1($rel1);

		$contactRepository
			->push($contact)
			->save();

		$data = $contactRepository->read()->fetchAll();
		$contact = end($data);
		$this->assertEquals($contact->address, "Washington DC");
		$this->assertEquals($contact->rel1->name, "sample rel3");
		$this->assertEquals($contact->rel1->rel2->name, "sample rel4");
		$phones = $contact->phones;
		$this->assertEquals(current($phones)->number, "666 888 999");
		$this->assertEquals(end($phones)->number, "111 222 333");
	}

	public function testContactRepositoryUpdate() {
		$contactRepository = new \Model\Contact\ContactRepository($this->emanager);

		$contacts = $contactRepository->read()->fetchAll();
		$contact = end($contacts);

		$contact->setAddress("Washington DC - updated");
		$phones = $contact->phones;

		current($phones)->setNumber("666 888 999 - updated");
		end($phones)->setNumber("111 222 333 - updated");

		$contact->rel1->setName("sample rel3 - updated");
		$contact->rel1->rel2->setName("sample rel4 - updated");

		$contactRepository->save();

		$data = $contactRepository->read()->fetchAll();
		$contact = end($data);
		$this->assertEquals($contact->address, "Washington DC - updated");
		$this->assertEquals($contact->rel1->name, "sample rel3 - updated");
		$this->assertEquals($contact->rel1->rel2->name, "sample rel4 - updated");
		$phones = $contact->phones;
		$this->assertEquals(current($phones)->number, "666 888 999 - updated");
		$this->assertEquals(end($phones)->number, "111 222 333 - updated");
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

		$this->assertEquals($data->name, "Parent item");
		$child = $data->child;
		$this->assertEquals($child->name, "Child item");

		$data->child->setName("Child item - updated");

		$myselfRepos->save();

		$data = $myselfRepos->read()->where("childID > ?", 0)->fetch();

		$this->assertEquals($data->name, "Parent item");
		$child = $data->child;
		$this->assertEquals($child->name, "Child item - updated");

		$this->database->query("TRUNCATE TABLE myself");
	}
}

?>
