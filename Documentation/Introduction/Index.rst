..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

The basic idea is to continuously fetch the oldest cached pages, invalidate their cache and curl the page so that they
are freshly cached. If there are prioritized pages (e.g. with changed visibility) they will be detected
automatically and processed first.

Motivation and Use case
=======================

TYPO3 comes with a sophisticated caching system that intelligently caches content and invalidates caches at the right
time. Nevertheless, there are special cases that are not or only very difficult to realize with the existing TYPO3
mechanisms.

An example: Content from another page, e.g. an image from the page property, is used in a teaser to this page and
displayed elsewhere on the website. If an editor now changes this property, only the cache of the page itself is
invalidated, but not that of all pages on which the teaser is present. If it is clear from the start on which pages
these teasers can be, this can be solved by a configuration in TYPO3; but if the editor has a free hand, the referencing
is not directly apparent and all caches would have to be invalidated with every change. This is of course not a
satisfactory solution.

This is where CacheFlow comes into play: the normal TYPO3 cache lifecycle remains unchanged; however, CacheFlow
cyclically invalidates all pages of the website in the background and rebuilds the cache directly. In this way, changes
such as the above-mentioned teaser or similar scenarios are processed promptly and the website is still performant at
all times.

Features
========

*   Invalidate and update cache entries for pages
*   Scan and prioritize pages and its content with changed visibility (when `startdate` or `enddate` becomes active)
*   Build page URIs and crawl pages to refresh cache entries
*   Dashboard widget to display cache flowing statistics
