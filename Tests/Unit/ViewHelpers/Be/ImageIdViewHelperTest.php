<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\Tests\Unit\ViewHelpers\Be;

use FGTCLB\PageBackendLayout\Exception\EmptyRequestException;
use FGTCLB\PageBackendLayout\Exception\WrongContextException;
use FGTCLB\PageBackendLayout\ViewHelpers\Be\ImageIdViewHelper;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;

final class ImageIdViewHelperTest extends UnitTestCase
{
    private const ARGUMENTS = [
        'table' => 'pages',
        'field' => 'media',
        'uid' => 1,
        'returnFirst' => true,
        'as' => 'image',
    ];

    #[Test]
    public function renderingWithoutRequestIsRejected(): void
    {
        $subject = new ImageIdViewHelper(new ConnectionPool());
        $subject->setRenderingContext(new RenderingContext());
        $subject->setArguments(self::ARGUMENTS);

        $this->expectException(EmptyRequestException::class);
        $this->expectExceptionCode(1740993136);

        $subject->render();
    }

    #[Test]
    public function renderingInFrontendRequestIsRejected(): void
    {
        $renderingContext = new RenderingContext();
        $renderingContext->setAttribute(
            ServerRequestInterface::class,
            (new ServerRequest('https://localhost/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
        );
        $subject = new ImageIdViewHelper(new ConnectionPool());
        $subject->setRenderingContext($renderingContext);
        $subject->setArguments(self::ARGUMENTS);

        $this->expectException(WrongContextException::class);
        $this->expectExceptionCode(1683720517279);

        $subject->render();
    }

    #[Test]
    public function renderingInRequestWithoutApplicationTypeIsRejected(): void
    {
        $renderingContext = new RenderingContext();
        $renderingContext->setAttribute(ServerRequestInterface::class, new ServerRequest('https://localhost/'));
        $subject = new ImageIdViewHelper(new ConnectionPool());
        $subject->setRenderingContext($renderingContext);
        $subject->setArguments(self::ARGUMENTS);

        $this->expectException(WrongContextException::class);
        $this->expectExceptionCode(1683720517279);

        $subject->render();
    }
}
