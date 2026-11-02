<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\Tests\Functional\PageLayout;

use FGTCLB\PageBackendLayout\Tests\Functional\AbstractPageBackendLayoutTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\View\FluidViewAdapter;

final class TemplateOverrideTest extends AbstractPageBackendLayoutTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
    }

    #[Test]
    public function pageModuleTemplateResolvesToTheExtensionsOverride(): void
    {
        $subject = $this->get(BackendViewFactory::class);

        $request = (new ServerRequest('https://localhost/typo3/module/web/layout'))
            ->withQueryParams(['id' => 1])
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', new Route('/module/web/layout', ['packageName' => 'typo3/cms-backend']));

        $view = $subject->create($request);

        self::assertInstanceOf(FluidViewAdapter::class, $view);
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();

        self::assertSame(
            ExtensionManagementUtility::extPath('page_backend_layout') . 'Resources/Private/Backend/Templates/PageLayout/PageLayout.html',
            $templatePaths->resolveTemplateFileForControllerAndActionAndFormat('PageLayout', 'PageLayout')
        );
    }

    #[Test]
    public function doktypePartialOfAnotherExtensionIsResolved(): void
    {
        $subject = $this->get(BackendViewFactory::class);

        $request = (new ServerRequest('https://localhost/typo3/module/web/layout'))
            ->withQueryParams(['id' => 1])
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', new Route('/module/web/layout', ['packageName' => 'typo3/cms-backend']));

        $view = $subject->create($request);

        self::assertInstanceOf(FluidViewAdapter::class, $view);
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();

        self::assertSame(
            ExtensionManagementUtility::extPath('page_backend_layout_test') . 'Resources/Private/Backend/Partials/PageLayout/Doktype1.html',
            $templatePaths->getPartialPathAndFilename('PageLayout/Doktype1')
        );
    }

    #[Test]
    public function coreGridPartialStaysAvailableForTheOverride(): void
    {
        $subject = $this->get(BackendViewFactory::class);

        $request = (new ServerRequest('https://localhost/typo3/module/web/layout'))
            ->withQueryParams(['id' => 2])
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', new Route('/module/web/layout', ['packageName' => 'typo3/cms-backend']));

        $view = $subject->create($request);

        self::assertInstanceOf(FluidViewAdapter::class, $view);
        $templatePaths = $view->getRenderingContext()->getTemplatePaths();

        self::assertStringStartsWith(
            ExtensionManagementUtility::extPath('backend') . 'Resources/Private/Partials/PageLayout/Grid.',
            $templatePaths->getPartialPathAndFilename('PageLayout/Grid')
        );
    }
}
