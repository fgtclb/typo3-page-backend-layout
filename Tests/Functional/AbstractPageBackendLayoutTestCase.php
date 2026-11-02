<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Base for all functional tests of the extension, loading it together with the fixture
 * extension that provides a page type partial for the overridden page module template.
 */
abstract class AbstractPageBackendLayoutTestCase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'fgtclb/page-backend-layout',
        'fgtclb/page-backend-layout-test',
    ];
}
