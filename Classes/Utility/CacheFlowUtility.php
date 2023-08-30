<?php

namespace F7\Cacheflow\Utility;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;

class CacheFlowUtility
{
    /**
     * @var int[]
     */
    public const EXCLUDED_DOKTYPES = [
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
     * @param float $averageExecutionTime
     * @return int
     */
    public static function estimateRoundRobin(int $totalPages, int $currentBatchSize, float $averageExecutionTime): int
    {
        return (int)round(($totalPages / $currentBatchSize) * $averageExecutionTime, 0);
    }

    /**
     * @param float $oldAverage
     * @param int $numberOfRuns
     * @param float $newValue
     * @return float
     */
    public static function calculateAverage(float $oldAverage, int $numberOfRuns, float $newValue): float
    {
        return round(($oldAverage * $numberOfRuns + $newValue) / ($numberOfRuns + 1), 0);
    }
}
