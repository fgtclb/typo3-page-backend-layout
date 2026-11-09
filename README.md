[![Latest Stable Version](https://poser.pugx.org/fgtclb/page-backend-layout/v/stable.svg?style=for-the-badge)](https://packagist.org/packages/fgtclb/page-backend-layout)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/13)
[![TYPO3 14.3](https://img.shields.io/badge/TYPO3-14.3-green.svg?style=for-the-badge)](https://get.typo3.org/version/14)
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
| 3.x                         | 13 + 14       | 8.2, 8.3, 8.4, 8.5 |

## Create a release (maintainers only)

Prerequisites:

* git binary
* ssh key allowed to push new branches to the repository
* GitHub command line tool `gh` installed and configured with user having permission to create pull requests.
* [`tailor`](https://github.com/TYPO3/tailor) and `composer` installed globally

**Create release**

> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main'.
> Set `RELEASE_VERSION` to release version working on, for example: '3.0.0'.
> Set `DEV_VERSION` to the version following the release, for example: '3.0.1'.

> [!IMPORTANT]
> Requires `GitHub cli tool` with personal token and
> maintainer permission on the extension repository.

```bash
echo '>> Create release' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION='3.0.0' ; \
  DEV_VERSION='3.0.1' ; \
  echo ">> Checkout branches" && \
  git checkout ${RELEASE_BRANCH} && \
  git fetch --all && \
  git pull --rebase && \
  echo ">> Create release ${RELEASE_VERSION}" && \
  git checkout -b release-${RELEASE_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${RELEASE_VERSION}\"/" Build/Scripts/runTests.sh && \
  sed -i "s/^  RELEASE_VERSION.*/  RELEASE_VERSION='${RELEASE_VERSION}' ; \\\\/" README.md && \
  sed -i "s/^  DEV_VERSION.*/  DEV_VERSION='${DEV_VERSION}' ; \\\\/" README.md && \
  tailor set-version ${RELEASE_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${RELEASE_VERSION}" && \
  echo "${RELEASE_VERSION}" > VERSION && \
  git add . && \
  git commit -m "[RELEASE] ${RELEASE_VERSION}" && \
  git push --set-upstream origin release-${RELEASE_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[RELEASE] ${RELEASE_VERSION}" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin && \
  git tag ${RELEASE_VERSION} -m "${RELEASE_VERSION}" && \
  git push origin ${RELEASE_VERSION} && \
  echo ">> Post-release - set dev version: ${DEV_VERSION}-dev" && \
  git checkout -b set-dev-version-${DEV_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${DEV_VERSION}-dev\"/" Build/Scripts/runTests.sh && \
  tailor set-version ${DEV_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${DEV_VERSION}-dev" && \
  echo "${DEV_VERSION}-dev" > VERSION && \
  git add . && \
  git commit -m "[TASK] Set dev version ${DEV_VERSION}" && \
  git push --set-upstream origin set-dev-version-${DEV_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[TASK] Set dev version \"${DEV_VERSION}-dev\"" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin
```

Pushing the tag triggers the `on push tags` workflow (`publish.yml`), which creates the upload package, creates the
GitHub release with the package attached and publishes the release to the TYPO3 Extension Repository.

## Supported Versions

| Version | Supported          | End of Support |
|---------|--------------------|----------------|
| 3.x     | :white_check_mark: | 2029-06-30     |
| 2.x     | :white_check_mark: | 2027-12-31     |
| < 2.0   | :x:                | support ended  |

## Security

Found a vulnerability? Please report it privately via our
[security report form](https://security.fgtclb.com) — **do not** open a public issue.
See [SECURITY.md](SECURITY.md) for the full vulnerability disclosure policy,
including what to expect and our safe harbor statement.

## Simplified EU Declaration of Conformity (Annex VI)

> Hereby, web-vision GmbH declares that the product with digital elements
> type FGTCLB: Page Backend Layout is in compliance with Regulation (EU) 2024/2847.
>
> The full text of the EU declaration of conformity is available at the
> following internet address:
> https://security.fgtclb.com/conformity/fgtclb/page-backend-layout/2.1.0/en/

The full declarations are also included in this repository:
[English](EU-Declaration-of-Conformity.md) ·
[Deutsch](EU-Konformitaetserklaerung.md).

## License

This extension is released under the [GPL-2.0-or-later](LICENSE) license.
