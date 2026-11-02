<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\Tests\Functional\ViewHelpers\Be;

use FGTCLB\PageBackendLayout\Tests\Functional\AbstractPageBackendLayoutTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final class ImageIdViewHelperTest extends AbstractPageBackendLayoutTestCase
{
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
            'expected' => '[first.jpg][second.jpg]|',
        ];
        yield 'no references of a record without any' => [
            'template' => 'AllReferences.html',
            'uid' => 2,
            'expected' => '|',
        ];
        yield 'first reference of the field in its sorting' => [
            'template' => 'FirstReference.html',
            'uid' => 1,
            'expected' => '[first.jpg]|',
        ];
        yield 'no first reference of a record without any' => [
            'template' => 'FirstReference.html',
            'uid' => 2,
            'expected' => '[none]|',
        ];
    }

    #[Test]
    #[DataProvider('referencesDataProvider')]
    public function referencesOfTheRecordAreProvidedToTheChildren(string $template, int $uid, string $expected): void
    {
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
