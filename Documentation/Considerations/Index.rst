..  include:: /Includes.rst.txt

..  _considerations:

=========================
Considerations and Issues
=========================

We intended to keep the extension as minimalistic as possible, so we decided to keep it simple
and deal with minor flaws as tradeoff. In our tests with huge websites (about 10.000 pages) the time for "round robin" was
about 1 hour, so the impact of the flaws should be small. Nevertheless we want to mention some of them:

* The TYPO3 caching mechanism might invalidate pages with valid `startdate` and `enddate` settings automatically. In this case our extra handling of these cases might be unnecessary. There should be more investigation on this in the future.

* When a page or content element is changed and the TYPO3 core cache invalidation is triggered (or when the cache is flushed by command), the `last_flowed` value will not change. As a consequence, the corresponding page(s) might be flowed earlier than pages, which might be more relevant at that point of time. One could consider listening to the `CacheFlushEvent` or use a `DataHandler` hook to avoid this.

Find a list of `open issues on Github
<https://github.com/f7media/cacheflow/issues>`__.
