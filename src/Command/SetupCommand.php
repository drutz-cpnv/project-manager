<?php

namespace App\Command;

use App\Services\SetupService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{

    protected static $defaultName = 'app:setup';

    public function __construct(private LoggerInterface $logger, private SetupService $setupService)
    {
        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Install default configuration !');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('App setup command running');
        $this->setupService->setup();
        return Command::SUCCESS;
    }

}