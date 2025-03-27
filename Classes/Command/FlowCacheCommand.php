<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace F7media\Cacheflow\Command;

use Doctrine\DBAL\Exception;
use F7media\Cacheflow\Domain\Repository\PageRepository;
use F7media\Cacheflow\Service\FlowCacheService;
use F7media\Cacheflow\Service\MessagingService;
use F7media\Cacheflow\Service\StatisticsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlowCacheCommand extends Command
{
    public function __construct(
        private readonly FlowCacheService $flowCacheService,
        private readonly PageRepository $pageRepository,
        private readonly StatisticsService $statisticsService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Flows the cache');
        $this->addArgument('batchSize', InputArgument::OPTIONAL, 'Number of pages per batch. (Default = 50)');
        $this->setHelp('');
    }

    /**
     * @throws Exception
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        $registry = GeneralUtility::makeInstance(Registry::class);
        $lastRun = (int)$registry->get('tx_cacheflow', 'FlowCacheCommand_lastRun', 0);
        $batchSize = ((int)$input->getArgument('batchSize') > 0) ? (int)$input->getArgument('batchSize') : 50;

        $pagesWithChangedVisibility = $this->pageRepository->findPagesWhoseVisibilityHasJustChanged($lastRun);
        $fillUpSize = $batchSize - count($pagesWithChangedVisibility);
        $fillUpPages = ($fillUpSize > 0) ? $this->pageRepository->fillupBatch($fillUpSize, $pagesWithChangedVisibility) : [];
        $this->flowCacheService->processPages(array_merge($pagesWithChangedVisibility, $fillUpPages));

        $registry->set('tx_cacheflow', 'FlowCacheCommand_lastRun', date('U'));
        $executionTime = microtime(true) - $startTime;
        $this->statisticsService->updateStatisticsInRegistry($batchSize, $executionTime);

        $messagingOutput = [
            'Batch size' => $batchSize,
            'Pages with changed visibility' => count($pagesWithChangedVisibility),
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
