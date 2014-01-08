<?php
/**
 * User: Martin
 * Date: 6.12.13
 * Time: 13:20
 */

namespace Model\Library;


use Nette\Database\Context;
use slimORM\BaseRepository;

class AuthorRepository extends BaseRepository {

	const TABLE = "author";
	const ENTITY = 'Model\Library\Entity\Author';

	/** Construct
	 * @param Context $database
	 */
	public function __construct(Context $database) {
		parent::__construct($database, AuthorRepository::TABLE, AuthorRepository::ENTITY);
	}
} 