[![Latest Stable Version](https://poser.pugx.org/fgtclb/page-backend-layout/v/stable.svg?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![TYPO3 12.4](https://img.shields.io/badge/TYPO3-12.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/12)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/13)
[![License](https://poser.pugx.org/fgtclb/page-backend-layout/license?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![Total Downloads](https://poser.pugx.org/fgtclb/page-backend-layout/downloads.svg?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![Monthly Downloads](https://poser.pugx.org/fgtclb/page-backend-layout/d/monthly?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)

# TYPO3 extension `page_backend_layout`

This extension provides a simple possibility of adding basic information to the
backend page view of TYPO3.

It is useful for giving information about page related information on top of the
view without editing the whole record. A ViewHelper is added for rendering images
into the view.

## Installation

Install with your flavour:

* composer
* [TER](https://extensions.typo3.org/extension/page_backend_layout/)
* Extension Manager

We prefer composer installation:
```bash
composer req fgtclb/page-backend-layout
```

|                  | URL                                                             |
|------------------|-----------------------------------------------------------------|
| **Repository:**  | https://github.com/fgtclb/typo3-page-backend-layout             |
| **Read online:** | https://docs.typo3.org/p/fgtclb/page-backend-layout/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/page_backend_layout/     |

## Version support

| Page Backend Layout Version | TYPO3 Version | PHP Version        |
|-----------------------------|---------------|--------------------|
| 1.x                         | 11 + 12       | \>=7.4             |
| 2.x                         | 12 + 13       | 8.1, 8.2, 8.3, 8.4 |

## Create a release (maintainers only)

Prerequisites:

* git binary
* ssh key allowed to push new branches to the repository
* GitHub command line tool `gh` installed and configured with user having permission to create pull requests.

**Prepare release locally**

> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main'.
> Set `RELEASE_VERSION` to release version working on, for example: '0.1.4'.

```shell
echo '>> Prepare release pull-request' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION='0.1.4' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  git checkout -b prepare-release-${RELEASE_VERSION} && \
  composer require --dev "typo3/tailor" && \
  ./.Build/bin/tailor set-version ${RELEASE_VERSION} && \
  composer remove --dev "typo3/tailor" && \
  git add . && \
  git commit -m "[TASK] Prepare release ${RELEASE_VERSION}" && \
  git push --set-upstream origin prepare-release-${RELEASE_VERSION} && \
  gh pr create --fill-verbose --base ${RELEASE_BRANCH} --title "[TASK] Prepare release for ${RELEASE_VERSION} on ${RELEASE_BRANCH}" && \
  git checkout main && \
  git branch -D prepare-release-${RELEASE_VERSION}
```

Check pull-request and the pipeline run.

**Merge approved pull-request and push version tag**

> Set `RELEASE_PR_NUMBER` with the pull-request number of the preparation pull-request.
> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main' (same as in previous step).
> Set `RELEASE_VERSION` to release version working on, for example: `0.1.4` (same as in previous step).

```shell
RELEASE_BRANCH='main' ; \
RELEASE_VERSION='0.1.4' ; \
RELEASE_PR_NUMBER='123' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  gh pr checkout ${RELEASE_PR_NUMBER} && \
  gh pr merge -rd ${RELEASE_PR_NUMBER} && \
  git tag ${RELEASE_VERSION} && \
  git push --tags
```

This triggers the `on push tags` workflow (`publish.yml`) which creates the upload package,
creates the GitHub release and also uploads the release to the TYPO3 Extension Repository.
