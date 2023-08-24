=========
cacheflow Extension
=========

The "cacheflow" extension provides a cache management solution for TYPO3 CMS, allowing you to flow the pages cache and
keep it up-to-date. It includes a CLI-command that can be executed from the command-line directly as well as be invoked
as Scheduler task. This process aims to ensure that the cache is always up-to-date and delivers the latest content to
your TYPO3 website visitors.

The basic idea is to continuously fetch the oldest cached pages, invalidate their cache and curl the page so that they
are freshly cached. If there are prioritized pages (e.g. with changed visibility or content) they will be detected
automatically and processed first.


Features
========

- Invalidate and update cache entries for pages
- Prioritize pages with changed visibility (when startdate or enddate becomes active)
- Optional: Prioritize pages with changed content
- Build page URIs and crawl pages to refresh cache entries
- Display cache flowing statistics

Installation
============

The extension currently supports TYPO3 v12.

Composer
--------

The installation via composer is recommended.

    $ composer require f7/cacheflow

TYPO3 Extension Repository
--------------------------

For non-composer projects, the extension is available in TER as extension key ``cacheflow`` and can be installed using
the extension manager.

Usage
=====

The "cacheflow" extension provides a command-line interface (CLI) command called `cacheflow:process` for cache
flowing operations. To execute the command, open a terminal or command prompt and navigate to the root directory of your
TYPO3 installation. Then run the following command

      $ bin/typo3 cacheflow:process

Options
---------------

- `--batchSize` (optional): Specifies the number of pages per execution. If nothing is set, the default value is 50.
- `--force-content` (optional): Forces the check for updated pages/content during cache flowing.

Scheduler Task
--------------

After you are familiar with the usage, you can create a Scheduler task, adapt the batch size to the performance of your
server and execute the task every minute (with no parallel execution allowed).

Contact
-------

For any inquiries or support requests, please contact F7 Media GmbH: https://www.f7.de
