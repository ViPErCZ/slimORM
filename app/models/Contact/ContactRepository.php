<?php
/**
 * User: Martin
 * Date: 13.12.13
 * Time: 7:48
 */

namespace Model\Contact;


use Model\Base\BaseModel;
use Model\Contact\Entity\Contact;
use Nette\Database\Context;
use Nette\Utils\Paginator;
use slimORM\BaseRepository;

class ContactRepository extends BaseModel {

	const ENTITY = '\Model\Contact\Entity\Contact';

	/**
	 * @param string $key
	 * @return \Model\Contact\Entity\Contact|NULL
	 */
	public function get($key) {
		$repository = $this->entityManager->getRepository(ContactRepository::ENTITY);
		return $repository->get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ContactRepository
	 */
	public function read(Paginator $paginator = NULL) {
		$repository = $this->entityManager->getRepository(ContactRepository::ENTITY);
		return $repository->read($paginator);
	}

	/**
	 * @param bool $needTransaction
	 * @param Contact $contact
	 * @return Contact|TRUE
	 */
	public function save($needTransaction = TRUE, Contact $contact = NULL) {
		$repository = $this->entityManager->getRepository(ContactRepository::ENTITY);
		return $repository->save($needTransaction, $contact);
	}
} 