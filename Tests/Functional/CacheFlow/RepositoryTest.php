<?php

declare(strict_types=1);

namespace F7\Cacheflow\Tests\Functional\CacheFlow;

use F7\Cacheflow\Domain\Repository\PageRepository;
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
        'testFindPagesWithUpdatedContent' => 2,
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
    public function testFindPagesWithUpdatedContent(): void
    {
        $this->importCSVDataSet(GeneralUtility::getFileAbsFileName('EXT:cacheflow/Tests/Functional/Fixtures/pages.csv'));
        $pageRepo = GeneralUtility::makeInstance(PageRepository::class);
        $pages = $pageRepo->findPagesWithUpdatedContent();
        self::assertEquals(self::EXPECTED_RESULTS['testFindPagesWithUpdatedContent'], count($pages));
    }
}
