<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\ViewHelpers\Be;

use Doctrine\DBAL\Exception;
use FGTCLB\PageBackendLayout\Exception\EmptyRequestException;
use FGTCLB\PageBackendLayout\Exception\WrongContextException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImageIdViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function initializeArguments(): void
    {
        $this->registerArgument('table', 'string', 'The table', true);
        $this->registerArgument('field', 'string', 'Field matching', true);
        $this->registerArgument('uid', 'int', 'Integer of current record', true);
        $this->registerArgument('returnFirst', 'bool', 'Whether to return only the first value', false, true);
        $this->registerArgument('as', 'string', 'Variable name to return', false, 'image');
    }

    /**
     * @throws FileDoesNotExistException
     * @throws Exception
     * @throws EmptyRequestException
     * @throws WrongContextException
     */
    public function render(): string
    {
        /**
         * @var array{
         *     table: string,
         *     field: string,
         *     uid: int,
         *     returnFirst: bool,
         *     as: string
         * } $arguments
         */
        $arguments = $this->arguments;
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->renderingContext;
        $renderChildrenClosure = $this->buildRenderChildrenClosure();

        if (!$renderingContext->hasAttribute(ServerRequestInterface::class)) {
            throw new EmptyRequestException(
                'ViewHelper can only be called with a proper request',
                1740993136
            );
        }
        $request = $renderingContext->getAttribute(ServerRequestInterface::class);
        $isBackendRequest = $request->getAttribute('applicationType')
            && ApplicationType::fromRequest($request)->isBackend();
        if (!$isBackendRequest) {
            throw new WrongContextException(
                'ViewHelper can only be called in backend context',
                1683720517279
            );
        }
        $templateVariableContainer = $renderingContext->getVariableProvider();
        $varName = $arguments['as'];
        $table = $arguments['table'];
        $field = $arguments['field'];
        $uid = $arguments['uid'];
        $returnFirst = $arguments['returnFirst'];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $result = $queryBuilder
            ->select('*')
            ->from('sys_file_reference')
            ->where(
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter($table, Connection::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter($field, Connection::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting_foreign')
            ->executeQuery();
        if ($returnFirst) {
            $row = $result->fetchAssociative();
            $value = $row !== false ? new FileReference($row) : false;
        } else {
            $value = [];
            while ($row = $result->fetchAssociative()) {
                $value[] = new FileReference($row);
            }
        }

        $templateVariableContainer->add($varName, $value);
        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($varName);
        return $output;
    }
}
