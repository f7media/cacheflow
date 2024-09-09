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

namespace F7media\Cacheflow\Tests\Unit\Hooks;

use F7media\Cacheflow\Utility\CacheFlowUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CalculationTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * @var array<string, array<string, int|float>|int>
     */
    protected const DATA_ROUND_ROBIN = [
        'INPUT' => [
            'totalPages' => 234,
            'currentBatchSize' => 20,
            'averageExecutionTime' => 32.3,
        ],
        'expectedResult' => 378,
    ];

    /**
     * @var array<string, array<string, float|int>|int>
     */
    protected const DATA_AVERAGE_CALC = [
        'INPUT' => [
            'oldAverage' => 56.5,
            'numberOfRuns' => 236,
            'newValue' => 83,
        ],
        'expectedResult' => 57,
    ];

    #[Test]
    public function validateRoundRobinCalculation(): void
    {
        $input = self::DATA_ROUND_ROBIN['INPUT'];
        self::assertEquals(
            self::DATA_ROUND_ROBIN['expectedResult'],
            CacheFlowUtility::estimateRoundRobin($input['totalPages'], $input['currentBatchSize'], $input['averageExecutionTime'])
        );
    }

    #[Test]
    public function validateAverageCalculation(): void
    {
        $input = self::DATA_AVERAGE_CALC['INPUT'];
        self::assertEquals(
            self::DATA_AVERAGE_CALC['expectedResult'],
            CacheFlowUtility::calculateAverage($input['oldAverage'], $input['numberOfRuns'], $input['newValue'])
        );
    }
}
