<?php

declare(strict_types=1);

namespace F7\Cacheflow\Command;

use Doctrine\DBAL\Exception;
use F7\Cacheflow\Domain\Repository\PageRepository;
use F7\Cacheflow\Service\FlowCacheService;
use F7\Cacheflow\Service\MessagingService;
use F7\Cacheflow\Service\StatisticsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlowCacheCommand extends Command
{
    public function __construct(
        private readonly FlowCacheService $flowCacheService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Flows the cache');
        $this->addArgument('batchSize', InputArgument::OPTIONAL, 'Number of pages per batch. (Default = 50)');
        $this->addOption('force-content', 'fc', InputOption::VALUE_NONE, 'Force check for updated pages/content.');
        $this->setHelp('');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $registry = GeneralUtility::makeInstance(Registry::class);
        $lastRun = (int)$registry->get('tx_cacheflow', 'FlowCacheCommand_lastRun', 0);
        $batchSize = ((int)$input->getArgument('batchSize') > 0) ? (int)$input->getArgument('batchSize') : 50;
        $includeContentUpdates = $input->getOption('force-content');

        $pagesWithChangedVisibility = $pageRepository->findPagesWhoseVisibilityHasJustChanged($lastRun);
        $pagesUpdated = ($includeContentUpdates) ? $pageRepository->findPagesWithUpdatedContent() : [];
        $pagesWithPrio = array_unique(array_merge($pagesWithChangedVisibility, $pagesUpdated));
        $fillUpSize = $batchSize - count($pagesWithPrio);
        $fillUpPages = ($fillUpSize > 0) ? $pageRepository->fillupBatch($fillUpSize, $pagesWithPrio) : [];
        $this->flowCacheService->processPages(array_merge($pagesWithPrio, $fillUpPages));

        $registry->set('tx_cacheflow', 'FlowCacheCommand_lastRun', date('U'));
        $executionTime = microtime(true) - $startTime;
        (new StatisticsService())->updateStatisticsInRegistry($batchSize, $executionTime);

        $messagingOutput = [
            'Batch size' => $batchSize,
            'Pages with changed visibility' => count($pagesWithChangedVisibility),
            'Pages with updated content' => ($includeContentUpdates) ? count($pagesUpdated) : 'disabled',
            'Pages filled up' => count($fillUpPages),
            'Execution Time (s)' => microtime(true) - $startTime,
        ];
        $messagingService = GeneralUtility::makeInstance(MessagingService::class);
        if (Environment::isCli()) {
            $io = new SymfonyStyle($input, $output);
            $messagingService->cliMessageStatistics($messagingOutput, $io);
        } else {
            $messagingService->flashMessageStatistics($messagingOutput);
        }

        return Command::SUCCESS;
    }

}
