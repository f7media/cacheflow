<?php

namespace F7\Cacheflow\Tests\Unit\Hooks;

use F7\Cacheflow\Utility\CacheFlowUtility;
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

    /**
     * @test
     */
    public function validateRoundRobinCalculation(): void
    {
        $input = self::DATA_ROUND_ROBIN['INPUT'];
        self::assertEquals(
            self::DATA_ROUND_ROBIN['expectedResult'],
            CacheFlowUtility::estimateRoundRobin($input['totalPages'], $input['currentBatchSize'], $input['averageExecutionTime'])
        );
    }

    /**
     * @test
     */
    public function validateAverageCalculation(): void
    {
        $input = self::DATA_AVERAGE_CALC['INPUT'];
        self::assertEquals(
            self::DATA_AVERAGE_CALC['expectedResult'],
            CacheFlowUtility::calculateAverage($input['oldAverage'], $input['numberOfRuns'], $input['newValue'])
        );
    }
}
