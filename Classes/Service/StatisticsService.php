<?php

declare(strict_types=1);

namespace F7\Cacheflow\Service;

use Doctrine\DBAL\Exception;
use F7media\Cacheflow\Domain\Repository\PageRepository;
use F7media\Cacheflow\Utility\CacheFlowUtility;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StatisticsService
{
    protected Registry $registry;

    public function __construct()
    {
        $this->registry = GeneralUtility::makeInstance(Registry::class);
    }

    /**
     * @param int $batchSize
     * @param float $executionTime
     */
    public function updateStatisticsInRegistry(int $batchSize, float $executionTime): void
    {
        $storedValues = $this->registry->get('tx_cacheflow', 'FlowCacheStatistics_storage');
        $currentStatistics = [
            'lastCompletedRun' => date('U'),
            'currentBatchSize' => $batchSize,
        ];
        if ($storedValues) {
            $storedStatistics = json_decode($storedValues, true);
            $currentStatistics['numberOfRuns'] = $storedStatistics['numberOfRuns'] + 1;
            $currentStatistics['averageExecutionTime'] = CacheFlowUtility::calculateAverage($storedStatistics['averageExecutionTime'], $storedStatistics['numberOfRuns'], $executionTime);
        } else {
            $currentStatistics['numberOfRuns'] = 1;
            $currentStatistics['averageExecutionTime'] = $executionTime;
        }

        $this->registry->set('tx_cacheflow', 'FlowCacheStatistics_storage', json_encode($currentStatistics));
    }

    /**
     * @return mixed[]
     * @throws Exception
     */
    public function composeWidgetOutput(): array
    {
        $data = $this->registry->get('tx_cacheflow', 'FlowCacheStatistics_storage');
        if (!$data || $data === []) return [];

        $statistics = json_decode($data, true);
        $output = [
            'currentBatchSize' => $statistics['currentBatchSize'],
            'lastCompletedRun' => date('d.m.Y H:i', (int)$statistics['lastCompletedRun']),
            'averageExecutionTime' => $statistics['averageExecutionTime'],
        ];
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $roundRobin = CacheFlowUtility::estimateRoundRobin($pageRepository->getAllRelevantPages(), $statistics['currentBatchSize'], $statistics['averageExecutionTime']);
        if ($roundRobin > 3600) {
            $output['estimationH'] = gmdate('H:i:s', $roundRobin);
        } elseif ($roundRobin > 60) {
            $output['estimationM'] = gmdate('i:s', $roundRobin);
        } else {
            $output['estimationS'] = $roundRobin;
        }

        $oldestFlowedPage = $pageRepository->getOldestCachedPageInSystem();
        if ($oldestFlowedPage > 0) {
            $output['oldestPage'] = date('d.m.Y H:i', $oldestFlowedPage);
        }
        return $output;
    }
}
