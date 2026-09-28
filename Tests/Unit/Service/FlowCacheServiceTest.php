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

namespace F7media\Cacheflow\Tests\Unit\Service;

use F7media\Cacheflow\Domain\Repository\PageRepository;
use F7media\Cacheflow\Event\PageCacheRefreshRequestedEvent;
use F7media\Cacheflow\Service\FlowCacheService;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FlowCacheServiceTest extends UnitTestCase
{
    #[Test]
    public function dispatchesEventAndUsesItsStatusWhenHandled(): void
    {
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects(self::once())
            ->method('updatePageLastCacheStatus')
            ->with(1, '200');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(function (PageCacheRefreshRequestedEvent $event) {
                self::assertSame(1, $event->getPageUid());
                self::assertSame('https://example.com/page-1', $event->getUri());
                $event->markHandled('200');

                return $event;
            });

        $subject = new class ($pageRepository, $eventDispatcher) extends FlowCacheService {
            public bool $invalidateCalled = false;

            public bool $crawlCalled = false;

            #[\Override]
            protected function buildPageUri(int $pid): string|bool
            {
                return 'https://example.com/page-1';
            }

            #[\Override]
            protected function invalidateCacheForPage(int $pid): bool
            {
                $this->invalidateCalled = true;

                return true;
            }

            #[\Override]
            protected function crawlPage(string $uri): int
            {
                $this->crawlCalled = true;

                return 200;
            }
        };

        $subject->processPages([1]);

        self::assertFalse($subject->invalidateCalled, 'invalidateCacheForPage must not run when the event was handled');
        self::assertFalse($subject->crawlCalled, 'crawlPage must not run when the event was handled');
    }

    #[Test]
    public function fallsBackToFlushAndCrawlWhenEventIsNotHandled(): void
    {
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects(self::once())
            ->method('updatePageLastCacheStatus')
            ->with(1, '200');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnArgument(0);

        $subject = new class ($pageRepository, $eventDispatcher) extends FlowCacheService {
            public bool $invalidateCalled = false;

            public bool $crawlCalled = false;

            #[\Override]
            protected function buildPageUri(int $pid): string|bool
            {
                return 'https://example.com/page-1';
            }

            #[\Override]
            protected function invalidateCacheForPage(int $pid): bool
            {
                $this->invalidateCalled = true;

                return true;
            }

            #[\Override]
            protected function crawlPage(string $uri): int
            {
                $this->crawlCalled = true;

                return 200;
            }
        };

        $subject->processPages([1]);

        self::assertTrue($subject->invalidateCalled, 'invalidateCacheForPage must run when nobody handled the event');
        self::assertTrue($subject->crawlCalled, 'crawlPage must run when nobody handled the event');
    }

    #[Test]
    public function reportsUriErrorWithoutDispatchingWhenUriCannotBeBuilt(): void
    {
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects(self::once())
            ->method('updatePageLastCacheStatus')
            ->with(1, 'URI_ERROR');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::never())->method('dispatch');

        $subject = new class ($pageRepository, $eventDispatcher) extends FlowCacheService {
            #[\Override]
            protected function buildPageUri(int $pid): string|bool
            {
                return false;
            }
        };

        $subject->processPages([1]);
    }
}
