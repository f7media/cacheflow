<?php

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

namespace F7media\Cacheflow\Utility;

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
    ];

    public static function estimateRoundRobin(int $totalPages, int $currentBatchSize, float $averageExecutionTime): int
    {
        return (int)round(($totalPages / $currentBatchSize) * $averageExecutionTime, 0);
    }

    public static function calculateAverage(float $oldAverage, int $numberOfRuns, float $newValue): float
    {
        return round(($oldAverage * $numberOfRuns + $newValue) / ($numberOfRuns + 1), 0);
    }
}
