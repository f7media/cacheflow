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

namespace F7media\Cacheflow\Tests\Functional\CacheFlow;

use F7media\Cacheflow\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case
 */
class RepositoryTest extends FunctionalTestCase
{
    /**
     * @var array<string, int>
     */
    protected const EXPECTED_RESULTS = [
        'testFindPagesWithUpdatedContent' => 6,
    ];

    /**
     * @var non-empty-string[]
     */
    protected array $coreExtensionsToLoad = [
        'dashboard',
    ];

    /**
     * @var non-empty-string[] Have cacheflow loaded
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/cacheflow',
    ];

    /**
     * @test
     */
    public function testFindPagesWhoseVisibilityHasJustChanged(): void
    {
        $lastRun = 1710181101;
        $this->importCSVDataSet(GeneralUtility::getFileAbsFileName('EXT:cacheflow/Tests/Functional/Fixtures/pages.csv'));
        $this->importCSVDataSet(GeneralUtility::getFileAbsFileName('EXT:cacheflow/Tests/Functional/Fixtures/tt_content.csv'));
        $pageRepo = GeneralUtility::makeInstance(PageRepository::class);
        $pages = $pageRepo->findPagesWhoseVisibilityHasJustChanged($lastRun);
        self::assertEquals(self::EXPECTED_RESULTS['testFindPagesWithUpdatedContent'], count($pages));
    }
}
