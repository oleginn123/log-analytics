<?php

namespace App\Command;

use App\Service\Import\LogsImportInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'log-analytics:import-logs',
    description: 'Import log entries into from a log file',
)]
class ImportLogsCommand extends Command
{
    public function __construct(
        private readonly LogsImportInterface $importer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('filePath', null, InputOption::VALUE_REQUIRED, 'Log file path')
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'Start position of reading log file.'
            )
            ->addOption(
                'pageSize',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of lines to process in one run.',
                100
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $filePath = strval($input->getOption('filePath')); // @phpstan-ignore-line
            if (empty($filePath)) {
                $io->error('Please specify --filePath option');

                return Command::FAILURE;
            }

            $pageSize = intval($input->getOption('pageSize')); // @phpstan-ignore-line
            $offset = $input->getOption('offset');

            $importResult = $offset === null
                ? $this->importer->importNext($filePath, $pageSize)
                : $this->importer->importPage(
                    $filePath,
                    intval($offset), // @phpstan-ignore-line
                    $pageSize
                );

            if ($importResult->isSuccess()) {
                $io->success($importResult->getCount() . ' log entries imported into database.');
            } else {
                $io->error('Unable to import log entries');
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
