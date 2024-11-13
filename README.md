[![Latest Stable Version](https://poser.pugx.org/fgtclb/page-backend-layout/v/stable.svg?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![TYPO3 11.5](https://img.shields.io/badge/TYPO3-11.5-green.svg?style=for-the-badge)](https://get.typo3.org/version/11)
[![TYPO3 12.4](https://img.shields.io/badge/TYPO3-12.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/12)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/13)
[![License](https://poser.pugx.org/fgtclb/page-backend-layout/license?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![Total Downloads](https://poser.pugx.org/fgtclb/page-backend-layout/downloads.svg?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![Monthly Downloads](https://poser.pugx.org/fgtclb/page-backend-layout/d/monthly?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)

# TYPO3 extension `page_backend_layout`

This extension provides the possibility to generate page type-based information in the header of the page module.
For each page type a partial can be added, which can be provided with the necessary information according to your own needs.

## Installation

There are different way for installation depending on your TYPO3 setup:

* [TER](https://extensions.typo3.org/extension/page_backend_layout/)
* Extension Manager
* composer

Preferred is composer installation:

```bash
composer req fgtclb/page-backend-layout
```

## Usage

The necessary steps to create a page type-based information block depend on your TYPO3 version.
Detailed information can be found here: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96812-NoFrontendTypoScriptBasedTemplateOverridesInTheBackend.html

### For TYPO3 version < 12

Add the path to your partial in your TypoScript configuration:

```HTML
module.tx_backend {
    view {
        partialRootPaths {
            example = EXT:example/Resources/Private/Backend/Partials/
        }
    }
}
```

Add good place for this configuration might be the `ext_typoscript_setup.typoscript` file in the  folder of your extension (e.g. site package) as this file will be included automatically if your extension is installed.

### For TYPO3 version >= 12

Add an alternative path for backend related layouts, templates and partials to your TsConfig:

```HTML
templates.typo3/cms-backend.1730990129 = fgtclb/academic-programs:Resources/Private/Backend
```

Add good place for this configuration might be the `page.tsconfig` file in the `Configuration` folder of your extension (e.g. site package) as this file will be included automatically if your extension is installed.

## Create your partial

```HTML
<f:if condition="{context.pageRecord.doktype} == <YOUR-DOKTYPE>">
    <div class="callout callout-notice">
        <!-- your code goes here -->
    </div>
</f:if>
```

## ViewHelpers

In case you need image preview, this extension ships a ViewHelper for getting
the image for you in backend context.
This ViewHelper only works in Backend-Context, as you normally don't need it in
frontend.

```HTML
<html
    data-namespace-typo3-fluid="true"
    lang="en"
    xmlns:f="http://typo3.org/ns/TYPO3Fluid/Fluid/ViewHelpers"
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
```

|                  | URL                                                             |
|------------------|-----------------------------------------------------------------|
| **Repository:**  | https://github.com/fgtclb/typo3-page-backend-layout             |
| **Read online:** | https://docs.typo3.org/p/fgtclb/page-backend-layout/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/page_backend_layout/     |
