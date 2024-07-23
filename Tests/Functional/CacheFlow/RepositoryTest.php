<?php

declare(strict_types=1);

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
     * @var array|string[]
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
