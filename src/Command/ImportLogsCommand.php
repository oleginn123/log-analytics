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
            ->addOption('filePath', null, InputOption::VALUE_OPTIONAL, 'Log file path')
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
            $importResult = $this->importer->import(
                $input->getOption('filePath'),
                $input->getOption('offset'),
                $input->getOption('pageSize')
            );

            if ($importResult->isSuccess()) {
                $io->success($importResult->getCount() . ' log entries imported into database.');
            } else {
                $io->error('Unable to import log entries');
            }
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
