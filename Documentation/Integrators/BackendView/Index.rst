.. include:: /Includes.rst.txt

.. _integration:

Integrate Backend View
======================

The extension provides the possibility to generate page type-based information
in the header of the web module.

For each page type, a partial can be added, which can be provided with the
necessary information according to your own wishes and ideas.

The following steps are necessary to create a page type-based information block:

Add your backend partials to the system
---------------------------------------

..  code-block:: typoscript
    :caption: EXT:example/Configuration/page.tsconfig

    templates.typo3/cms-backend.1740993701933 = vendor/example:Resources/Private/Backend/Templates

Create your partial
-------------------

..  code-block:: html
    :caption: EXT:example/Resources/Private/Backend/Partials/PageLayout/Doktype<YOUR-DOKTYPE>.html

    <f:if condition="{context.pageRecord.doktype} == <YOUR-DOKTYPE>">
        <div class="callout callout-notice">
            <!-- your code goes here -->
        </div>
    </f:if>

ViewHelpers
-----------

In case you need image preview, this extension ships a ViewHelper for getting
the image for you in backend context.
This ViewHelper only works in Backend-Context, as you normally don't need it in
frontend.

..  code-block:: html
    :caption: EXT:example/Resources/Private/Backend/Partials/PageLayout/Doktype<YOUR-DOKTYPE>.html
    :linenos:
    :emphasize-lines: 5,11

    <html
        data-namespace-typo3-fluid="true"
        lang="en"
        xmlns:f="https://typo3.org/ns/TYPO3Fluid/Fluid/ViewHelpers"
        xmlns:pbl="http://typo3.org/ns/FGTCLB/PageBackendLayout/ViewHelpers"
    >
    <f:if condition="{context.pageRecord.doktype} == <YOUR-DOKTYPE>">
        <div class="callout callout-notice">
            <f:if condition="{context.pageRecord.media}">
                <f:then>
                    <pbl:be.imageId as="thumb" field="media" returnFirst="true" table="pages" uid="{context.pageRecord.uid}">
                        <f:if condition="{thumb}">
                            <f:image image="{thumb.originalFile}" width="200"/>
                        </f:if>
                    </pbl:be.imageId>
                </f:then>
                <f:else><span>No image</span></f:else>
            </f:if>
            <!-- your code goes here -->
        </div>
    </f:if>
