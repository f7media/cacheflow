..  include:: /Includes.rst.txt

..  _installation:

============
Installation
============

The extension currently supports TYPO3 v12 and v13.

Composer
--------

The installation via composer is recommended.

..  code-block:: bash

    composer require f7media/cacheflow


TYPO3 Extension Repository
--------------------------

For non-composer projects, the extension is available in TER as extension key ``cacheflow`` and can be installed using
the extension manager.

Update the database scheme
--------------------------

Open your TYPO3 backend with :ref:`system maintainer <t3start:system-maintainer>`
permissions.

In the module menu to the left navigate to :guilabel:`Admin Tools > Maintanance`,
then click on :guilabel:`Analyze database` and create all.

See also :ref:`Run the database analyser <t3upgrade:run_the_database_analyser>`