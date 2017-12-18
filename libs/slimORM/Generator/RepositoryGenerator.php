<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 4.3.16
 * Time: 10:13
 */

namespace slimORM\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\Utils\DateTime;
use slimORM\Generator\Sql\Table;
use slimORM\AbstractRepository;
use slimORM\EntityManager;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;

/**
 * Class RepositoryGenerator
 * @package slimORM\Generator
 * @author Martin Chudoba <info@vipersoftware.net>
 */
class RepositoryGenerator implements IGenerateAble {

	/** @var string */
	protected $path;

	/** @var Table */
	protected $table;

	/** suffix name */
	const suffix = 'Repository';

	/**
	 * RepositoryGenerator constructor.
	 * @param string $path
	 * @param Table $table
	 */
	public function __construct($path, Table $table) {
		$this->path = $path;
		$this->table = $table;
	}

	/**
	 * @return bool
	 */
	public function generate(): bool {
		$tableName = $this->table->getName();
		$className = ucfirst($tableName) . self::suffix;

		$entity = new ClassType($className);
		$current = new DateTime();
		$entity->setComment('Class ' . $className . PHP_EOL . '@generated ' . $current);
		$entity->addExtend(AbstractRepository::class);
		$entity->setAbstract(false);

		$parameter = new Parameter('entityManager');
		$parameter->setTypeHint(EntityManager::class);

		$entity->addConstant('ENTITY', "\\" . ucfirst($tableName));
		$entity->addMethod('__construct')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array($className . ' constructor', "@param \\slimORM\\EntityManager \$entityManager")))
			->setBody("\$this->entityManager = \$entityManager;\n\$this->entity = " . $className . '::ENTITY;');

		$parameter = new Parameter('key');
		$entity->addMethod('get')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array('@param $key', '@return null|' . ucfirst($tableName), "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->get(\$key);");

		$parameter = new Parameter('paginator');
		$parameter->setTypeHint(Paginator::class);
		//$parameter->setOptional(true);
		$parameter->setDefaultValue(null);
		$entity->addMethod('read')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array("@param \\Nette\\Utils\\Paginator|NULL \$paginator", '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->read(\$paginator);");

		$parameter = new Parameter('columns');
		$entity->addMethod('select')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array('@param string $columns', '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nif (\$this->containSelectPrimaryKey(\$columns) === false) {\n\t\$columns .= \",\" . \$repository->getPrimaryKey();\n}\nreturn \$repository->select(\$columns);");

		$entity->addMethod('save')
			->setComment(implode(PHP_EOL, array('@return bool', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->save();");

		$parameter = new Parameter('entity');
		$parameter->setTypeHint(Entity::class);
		$entity->addMethod('push')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array("@param \\slimORM\\Entity\\Entity \$entity", '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\n\$repository->push(\$entity);\nreturn \$repository;");

		$entity->addMethod('fetchAll')
			->setComment(implode(PHP_EOL, array('@return ' . ucfirst($tableName) . '[]|NULL', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->fetchAll();");

		$entity->addMethod('fetch')
			->setComment(implode(PHP_EOL, array('@return ' . ucfirst($tableName) . '|array', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->fetch();");

		$parameter = new Parameter('key');
		$parameter2 = new Parameter('value');
		//$parameter2->setOptional(true);
		$parameter2->setDefaultValue(null);
		$entity->addMethod('fetchPairs')
			->setParameters(array($parameter, $parameter2))
			->setComment(implode(PHP_EOL, array('@param string $key', '@param null|string $value', '@return array', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->fetchPairs(\$key, \$value);");

		$parameter = new Parameter('condition');
		$parameter2 = new Parameter('parameters');
		//$parameter2->setOptional(true);
		$parameter2->setDefaultValue(array());
		$entity->addMethod('where')
			->setParameters(array($parameter, $parameter2))
			->setComment(implode(PHP_EOL, array('@param string $condition', '@param array $parameters', '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->where(\$condition, \$parameters);");

		$parameter = new Parameter('key');
		$entity->addMethod('wherePrimary')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array('@param string $key', '@return $this', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->wherePrimary(\$key);");

		$parameter = new Parameter('column');
		//$parameter->setOptional(true);
		$parameter->setDefaultValue(null);
		$entity->addMethod('count')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array('@param string|null $column', '@return int', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->count(\$column);");

		$entity->addMethod('getLastInsertID')
			->setComment(implode(PHP_EOL, array('@return int|null', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->getLastInsertID();");

		$entity->addMethod('clear')
			->setComment(implode(PHP_EOL, array("@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody('return $this->entityManager->getRepository($this->entity)->clear();');

		$parameter = new Parameter('limit');
		$parameter2 = new Parameter('offset');
		//$parameter2->setOptional(true);
		$parameter2->setDefaultValue(null);
		$entity->addMethod('limit')
			->setParameters(array($parameter, $parameter2))
			->setComment(implode(PHP_EOL, array('@param string $limit', '@param null|int $offset', '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->limit(\$limit, \$offset);");

		$parameter = new Parameter('columns');
		$entity->addMethod('order')
			->setParameters(array($parameter))
			->setComment(implode(PHP_EOL, array('@param string $columns', '@return ' . ucfirst($tableName) . 'Repository', "@throws \\slimORM\\Exceptions\\RepositoryException")))
			->setBody("\$repository = \$this->entityManager->getRepository(\$this->entity);\nreturn \$repository->order(\$columns);");

		return $this->save($className, $entity);
	}

	/**
	 * @param $className
	 * @param ClassType $entity
	 * @return bool
	 */
	protected function save($className, ClassType $entity): bool {
		$handle = null;
		if (($handle = @fopen($this->path . '/' . $className . '.php', 'w')) !== null) {
			fwrite($handle, "<?php\n\n");
			fwrite($handle, $entity);
			fclose($handle);
			return true;
		} else {
			return false;
		}
	}
}