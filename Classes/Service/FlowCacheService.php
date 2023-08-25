<?php

declare(strict_types=1);

namespace F7\Cacheflow\Service;

use F7\Cacheflow\Domain\Repository\PageRepository;
use GuzzleHttp\Exception\ClientException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Domain\Repository\PageRepository as CorePageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlowCacheService
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
    )
    {
    }

    /**
     * @param array $pages
     * @throws NoSuchCacheException
     * @throws SiteNotFoundException
     */
    public function processPages(array $pages): void
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        foreach ($pages as $uid) {
            $this->invalidateCacheForPage($uid);
            $uri = $this->buildPageUri($uid);
            if (is_string($uri)) {
                $this->crawlPage($uri);
            }

            $pageRepository->updatePageLastCacheStatus($uid);
        }
    }

    /**
     * @param int $pid
     * @throws NoSuchCacheException
     */
    protected function invalidateCacheForPage(int $pid): void
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->getCache('pages')->flushByTag('pageId_' . $pid);
    }

    /**
     * @param int $pid
     * @return string|bool
     * @throws SiteNotFoundException
     */
    protected function buildPageUri(int $pid): string|bool
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        try {
            $site = $siteFinder->getSiteByPageId($pid);
        } catch (SiteNotFoundException) {
            $corePageRepository = GeneralUtility::makeInstance(CorePageRepository::class);
            $page = $corePageRepository->getPage($pid, true);
            if (isset($page['sys_language_uid']) && $page['sys_language_uid'] > 0) {
                $site = $siteFinder->getSiteByRootPageId($page['l10n_parent']);
            } else {
                return false;
            }
        }

        $router = $site->getRouter();
        return (string)$router->generateUri($pid);
    }

    /**
     * @param string $uri
     * @return int
     */
    protected function crawlPage(string $uri): int
    {
        try {
            $statusCode = $this->requestFactory->request($uri, 'GET')->getStatusCode();
            return $statusCode;
        } catch (ClientException  $e) {
            return $e->getCode();
        }
    }

}
