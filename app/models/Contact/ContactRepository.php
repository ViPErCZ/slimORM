<?php
/**
 * User: Martin
 * Date: 13.12.13
 * Time: 7:48
 */

namespace Model\Contact;


use Nette\Database\Connection;
use slimORM\BaseRepository;

class ContactRepository extends BaseRepository {

	const TABLE = "contact";
	const ENTITY = '\Model\Contact\Entity\Contact';

	/**
	 * Constructor
	 * @param Connection $connection
	 */
	public function __construct(Connection $connection) {
		parent::__construct($connection, ContactRepository::TABLE, ContactRepository::ENTITY);
	}
} 