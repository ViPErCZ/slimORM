<?php
/**
 * User: Martin Chudoba
 * Date: 30.11.15
 * Time: 20:54
 */

namespace Command\Generators;

use slimORM\Generator\EntityGenerator;
use slimORM\Generator\RepositoryGenerator;
use slimORM\Generator\SqlParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModelGeneratorCommand
 * @package Command\Generators
 */
class ModelGeneratorCommand extends Command {

	/**
	 *
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 */
	protected function configure() {
		$this->setName('app:generate-model')
			->setDescription('Auto-generate model classes.');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws \RuntimeException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$output->writeln('Begin generate...');

		$sqlParser = new SqlParser(WWW_DIR . "/navrhy/slimorm.sql");
		$tables = $sqlParser->getTables();

		if (!mkdir(APP_DIR . '/generated/') && !is_dir(APP_DIR . '/generated/')) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', APP_DIR . "/generated/"));
		}
		if (!mkdir(APP_DIR . '/generated/Base') && !is_dir(APP_DIR . '/generated/Base')) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', APP_DIR . '/generated/Base'));
		}

		foreach ($tables as $table) {
			$entityGenerator = new EntityGenerator(APP_DIR . '/generated/Base/Entity', $table);
			$entityGenerator->generate();

			$repositoryGenerator = new RepositoryGenerator(APP_DIR . '/generated/Base/', $table);
			$repositoryGenerator->generate();
		}

		$output->writeln('Finish generate...');

		return 0;
	}


}