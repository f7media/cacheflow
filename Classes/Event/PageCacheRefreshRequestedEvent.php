<?php

declare(strict_types=1);

namespace F7media\Cacheflow\Event;

/**
 * Dispatched before cacheflow invalidates and re-crawls a page's cache.
 *
 * A listener that can refresh the page's cache more safely than a plain
 * flush-then-crawl (e.g. a cache-backend-specific "prime before swap"
 * implementation) should call markHandled() so FlowCacheService skips its
 * own flush/crawl fallback for this page.
 */
final class PageCacheRefreshRequestedEvent
{
    private bool $handled = false;

    private ?string $status = null;

    public function __construct(
        private readonly int $pageUid,
        private readonly string $uri,
    ) {}

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function markHandled(string $status): void
    {
        $this->handled = true;
        $this->status = $status;
    }
}
