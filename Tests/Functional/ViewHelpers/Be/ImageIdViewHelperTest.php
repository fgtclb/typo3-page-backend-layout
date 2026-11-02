<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\Tests\Functional\ViewHelpers\Be;

use FGTCLB\PageBackendLayout\Tests\Functional\AbstractPageBackendLayoutTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final class ImageIdViewHelperTest extends AbstractPageBackendLayoutTestCase
{
    protected array $coreExtensionsToLoad = [
        'workspaces',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_file.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
    }

    public static function referencesDataProvider(): \Generator
    {
        yield 'all references of the field in their sorting, without deleted and hidden ones' => [
            'template' => 'AllReferences.html',
            'uid' => 1,
            'workspace' => 0,
            'expected' => '[first.jpg][second.jpg]|',
        ];
        yield 'references of the workspace with changed, new and deleted ones, in their sorting' => [
            'template' => 'AllReferences.html',
            'uid' => 1,
            'workspace' => 1,
            'expected' => '[workspace-changed.jpg][workspace-new.jpg]|',
        ];
        yield 'no references of a record without any' => [
            'template' => 'AllReferences.html',
            'uid' => 2,
            'workspace' => 0,
            'expected' => '|',
        ];
        yield 'first reference of the field in its sorting' => [
            'template' => 'FirstReference.html',
            'uid' => 1,
            'workspace' => 0,
            'expected' => '[first.jpg]|',
        ];
        yield 'first reference of the workspace in its sorting' => [
            'template' => 'FirstReference.html',
            'uid' => 1,
            'workspace' => 1,
            'expected' => '[workspace-changed.jpg]|',
        ];
        yield 'no first reference of a record without any' => [
            'template' => 'FirstReference.html',
            'uid' => 2,
            'workspace' => 0,
            'expected' => '[none]|',
        ];
    }

    #[Test]
    #[DataProvider('referencesDataProvider')]
    public function referencesOfTheRecordAreProvidedToTheChildren(
        string $template,
        int $uid,
        int $workspace,
        string $expected,
    ): void {
        $this->get(Context::class)->setAspect('workspace', new WorkspaceAspect($workspace));
        $request = (new ServerRequest('https://localhost/typo3/module/web/layout'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $view = $this->get(ViewFactoryInterface::class)->create(new ViewFactoryData(
            templatePathAndFilename: __DIR__ . '/Fixtures/Templates/' . $template,
            request: $request,
        ));
        $view->assign('uid', $uid);

        self::assertSame($expected, trim($view->render()));
    }
}
