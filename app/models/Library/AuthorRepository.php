<?php
/**
 * User: Martin
 * Date: 6.12.13
 * Time: 13:20
 */

namespace Model\Library;


use Nette\Database\Connection;
use slimORM\BaseRepository;

class AuthorRepository extends BaseRepository {

	const TABLE = "author";
	const ENTITY = 'Model\Library\Entity\Author';

	/** Construct
	 * @param Connection $database
	 */
	public function __construct(Connection $database) {
		parent::__construct($database, AuthorRepository::TABLE, AuthorRepository::ENTITY);
	}
} 