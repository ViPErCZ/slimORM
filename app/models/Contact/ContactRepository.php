<?php
/**
 * User: Martin
 * Date: 13.12.13
 * Time: 7:48
 */

namespace Model\Contact;


use Nette\Database\Context;
use slimORM\BaseRepository;

class ContactRepository extends BaseRepository {

	const TABLE = "contact";
	const ENTITY = '\Model\Contact\Entity\Contact';

	/**
	 * Constructor
	 * @param Context $connection
	 */
	public function __construct(Context $connection) {
		parent::__construct($connection, ContactRepository::TABLE, ContactRepository::ENTITY);
	}
} 