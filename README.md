![tests core v12](https://github.com/f7media/cacheflow/actions/workflows/testscorev12.yml/badge.svg)

# TYPO3 Extension ``cacheflow``

The "cacheflow" extension provides a cache management solution for TYPO3 CMS, allowing you to flow the pages cache and
keep it up-to-date. It includes a CLI-command that can be executed from the command-line directly as well as be invoked
as Scheduler task. This process aims to ensure that the cache is always up-to-date and delivers the latest content to
your TYPO3 website visitors.

## Introduction

The basic idea is to continuously fetch the oldest cached pages, invalidate their cache and curl the page so that they
are freshly cached. If there are prioritized pages (e.g. with changed visibility or content) they will be detected
automatically and processed first.

### Motivation / Use case

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

### Features

- Invalidate and update cache entries for pages
- Scan and prioritize pages and its content with changed visibility (when `startdate` or `enddate` becomes active)
- Build page URIs and crawl pages to refresh cache entries
- Dashboard widget to display cache flowing statistics

## Installation

The extension currently supports TYPO3 v12.

### Composer

The installation via composer is recommended.

    $ composer require f7media/cacheflow

### TYPO3 Extension Repository

For non-composer projects, the extension is available in TER as extension key ``cacheflow`` and can be installed using
the extension manager.

## Usage

The "cacheflow" extension provides a command-line interface (CLI) command called `cacheflow:process` for cache
flowing operations. To execute the command, open a terminal or command prompt and navigate to the root directory of your
TYPO3 installation. Then run the following command

      $ bin/typo3 cacheflow:process

### Options

- `--batchSize` (optional): Specifies the number of pages per execution. If nothing is set, the default value is 50.

## Scheduler Task

After you are familiar with the usage, you can create a Scheduler task, adapt the batch size to the performance of your
server and execute the task every minute (with no parallel execution allowed).

## Considerations and Issues

We intended to keep the extension as minimalistic as possible, so we decided to keep it simple
and deal with minor flaws as tradeoff. In our tests with huge websites (about 10.000 pages) the time for "round robin" was
about 1 hour, so the impact of the flaws should be small. Nevertheless we want to mention some of them:

* The TYPO3 caching mechanism might invalidate pages with valid `startdate` and `enddate` settings automatically. In this case 
case our extra handling of these cases might be unnecessary. There should be more investigation on this in the future.
* When a page or content element is changed and the TYPO3 core cache invalidation is triggered (or when the cache is flushed 
by command), the `last_flowed` value will not change. As a consequence, the corresponding page(s) might be flowed earlier
than pages, which might be more relevant at that point of time. One could consider listening to the `CacheFlushEvent` or 
use a `DataHandler` hook to avoid this.

If you find an issue [please report here](https://github.com/f7media/cacheflow/issues).

### Contact

For any inquiries or support requests, please contact F7 Media GmbH: https://www.f7.de

|                  | URL                                                     |
|------------------|---------------------------------------------------------|
| **Repository:**  | https://github.com/f7media/cacheflow/                   |
| **Read online:** | https://docs.typo3.org/p/cacheflow/main/en-us/          |
| **TER:**         | https://extensions.typo3.org/extension/cacheflow/ |

