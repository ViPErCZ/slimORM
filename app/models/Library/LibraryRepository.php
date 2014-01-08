<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 14:54
 */

namespace Model\Library;


use Nette\Database\Context;
use slimORM\BaseRepository;

class LibraryRepository extends BaseRepository {

	const TABLE = "library";
	const ENTITY = 'Model\Library\Entity\Library';

	/** Construct
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(Context $database) {
		parent::__construct($database, LibraryRepository::TABLE, LibraryRepository::ENTITY);
	}
} 