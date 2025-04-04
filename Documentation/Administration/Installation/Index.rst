.. include:: /Includes.rst.txt

.. _installation:

Installation
============

The extension needs to be installed as any other extension of TYPO3 CMS. Get the
extension by one of the following methods:

#.  **Use composer**:
    Run

    .. code-block:: bash

        composer require fgtclb/page-backend-layout

    in your TYPO3 installation.

#.  **Get it from the Extension Manager**:
    Switch to the module :guilabel:`Admin Tools > Extensions`.
    Switch to :guilabel:`Get Extensions` and search for the extension key
    *page_backend_layout* and import the extension from the repository.

#. **Get it from typo3.org**:
    You can always get current version from `TER`_ by downloading the zip
    version. Upload the file afterwards in the Extension Manager.

For overriding PageLayouts, follow the instructions at the
:ref:`Integrators section <integration>`.

.. _TER: https://extensions.typo3.org/extension/page_backend_layout

Compatibility
-------------

Page Backend Layout supports:

..  csv-table:: Changes
    :header: "Page Backend Layout version","TYPO3 Version","PHP version"
    :file: Files/versionSupport.csv
