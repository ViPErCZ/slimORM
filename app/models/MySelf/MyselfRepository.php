<?php
/**
 * User: Martin
 * Date: 12.12.13
 * Time: 13:38
 */

namespace Model\MySelf;


use Nette\Database\Connection;
use slimORM\BaseRepository;

class MyselfRepository extends BaseRepository {

	const TABLE = "myself";
	const ENTITY = '\Model\Myself\Entity\Myself';

	/**
	 * Construct
	 * @param Connection $connection
	 */
	public function __construct(Connection $connection) {
		parent::__construct($connection, MyselfRepository::TABLE, MyselfRepository::ENTITY);
	}
} 