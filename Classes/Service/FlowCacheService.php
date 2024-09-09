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

namespace F7media\Cacheflow\Service;

use F7media\Cacheflow\Domain\Repository\PageRepository;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Domain\Repository\PageRepository as CorePageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlowCacheService
{
    /**
     * @param mixed[] $pages
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
     * @throws NoSuchCacheException
     */
    protected function invalidateCacheForPage(int $pid): void
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->getCache('pages')->flushByTag('pageId_' . $pid);
    }

    /**
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

    protected function crawlPage(string $uri): int
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        try {
            return $requestFactory->request($uri, 'GET')->getStatusCode();
        } catch (ServerException|ClientException|TooManyRedirectsException $exception) {
            return $exception->getCode();
        }
    }
}
