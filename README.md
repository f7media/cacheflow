# TYPO3 Extension ``cacheflow``

The "cacheflow" extension provides a cache management solution for TYPO3 CMS, allowing you to flow the pages cache and
keep it up-to-date. It includes a CLI-command that can be executed from the command-line directly as well as be invoked
as Scheduler task. This process aims to ensure that the cache is always up-to-date and delivers the latest content to
your TYPO3 website visitors.

The basic idea is to continuously fetch the oldest cached pages, invalidate their cache and curl the page so that they
are freshly cached. If there are prioritized pages (e.g. with changed visibility or content) they will be detected
automatically and processed first.

## Motivation / Use case

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

## Features

- Invalidate and update cache entries for pages
- Prioritize pages with changed visibility (when startdate or enddate becomes active)
- Optional: Prioritize pages with changed content
- Build page URIs and crawl pages to refresh cache entries
- Display cache flowing statistics

## Installation

The extension currently supports TYPO3 v12.

## Composer

The installation via composer is recommended.

    $ composer require f7/cacheflow

## TYPO3 Extension Repository

For non-composer projects, the extension is available in TER as extension key ``cacheflow`` and can be installed using
the extension manager.

## Usage

The "cacheflow" extension provides a command-line interface (CLI) command called `cacheflow:process` for cache
flowing operations. To execute the command, open a terminal or command prompt and navigate to the root directory of your
TYPO3 installation. Then run the following command

      $ bin/typo3 cacheflow:process

## Options

- `--batchSize` (optional): Specifies the number of pages per execution. If nothing is set, the default value is 50.
- `--force-content` (optional): Forces the check for updated pages/content during cache flowing.

## Scheduler Task

After you are familiar with the usage, you can create a Scheduler task, adapt the batch size to the performance of your
server and execute the task every minute (with no parallel execution allowed).

## Contact

For any inquiries or support requests, please contact F7 Media GmbH: https://www.f7.de
