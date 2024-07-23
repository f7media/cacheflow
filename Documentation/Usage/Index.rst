..  include:: /Includes.rst.txt

..  _usage:

=====
Usage
=====

he "cacheflow" extension provides a command-line interface (CLI) command called `cacheflow:process` for cache
flowing operations. To execute the command, open a terminal or command prompt and navigate to the root directory of your
TYPO3 installation. Then run the following command

..  code-block:: bash

    typo3 cacheflow:process

The initial run will flow all existing pages, so will take some minutes. After this only the relevant pages are flowed.

.. figure:: /Images/console_output.png
   :class: with-shadow

See also :ref:`Run a command from the command line <t3coreapi:symfony-console-commands-cli>` for further explanation of
TYPO3 console commands.

Options
_______

`--batchSize` (optional): Specifies the number of pages per execution. If nothing is set, the default value is 50.

Scheduler Task
==============

After you are familiar with the usage, you can create a Scheduler task, adapt the batch size to the performance of your
server and execute the task every minute (with no parallel execution allowed).

Check the TYPO3 manual for :ref:`Adding or editing a task <typo3/cms-scheduler:adding-editing-task>`

Dashboard widget
================

System maintainers can keep an overview by adding a prepared dashboard widget to the TYPO3 backend.

Just use the wizard to add the widget `CacheFlow Information`.

.. figure:: /Images/cacheflow_widget.png
   :class: with-shadow

More on :ref:`Adding Dashboard <typo3/cms-dashboard:adding-dashboard>`