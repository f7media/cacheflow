<?php

namespace F7\Cacheflow\Utility;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;

class CacheFlowUtility
{
    /**
     * @var int[]
     */
    const EXCLUDED_DOKTYPES = [
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
        PageRepository::DOKTYPE_BE_USER_SECTION,
        PageRepository::DOKTYPE_MOUNTPOINT,
        PageRepository::DOKTYPE_SPACER,
        PageRepository::DOKTYPE_SYSFOLDER,
        PageRepository::DOKTYPE_RECYCLER,
    ];

    /**
     * @param int $totalPages
     * @param int $currentBatchSize
     * @param int $averageExecutionTime
     * @return int
     */
    public static function estimateRoundRobin(int $totalPages, int $currentBatchSize, int $averageExecutionTime): int
    {
        return (int)round(($totalPages / $currentBatchSize) * $averageExecutionTime, 0);
    }

    /**
     * @param float $average
     * @param int $numberOfRuns
     * @param float $newValue
     * @return float
     */
    public static function calculateAverage(float $average, int $numberOfRuns, float $newValue): float
    {
        return round(($average * $numberOfRuns + $newValue) / ($numberOfRuns + 1), 0);
    }
}
