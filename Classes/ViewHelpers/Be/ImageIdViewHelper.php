<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\ViewHelpers\Be;

use Doctrine\DBAL\Exception;
use FGTCLB\PageBackendLayout\Exception\EmptyRequestException;
use FGTCLB\PageBackendLayout\Exception\WrongContextException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Versioning\VersionState;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class ImageIdViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly Context $context,
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

        $rows = $this->findReferenceRows($table, $field, $uid);
        if ($returnFirst) {
            $value = $rows !== [] ? new FileReference($rows[0]) : false;
        } else {
            $value = [];
            foreach ($rows as $row) {
                $value[] = new FileReference($row);
            }
        }

        $templateVariableContainer->add($varName, $value);
        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($varName);
        return $output;
    }

    /**
     * Returns the reference rows of the record field as the current workspace sees them, in their sorting.
     *
     * @return list<array<string, mixed>>
     */
    private function findReferenceRows(string $table, string $field, int $uid): array
    {
        $workspaceId = (int)$this->context->getPropertyFromAspect('workspace', 'id', 0);
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder->getRestrictions()->add(new WorkspaceRestriction($workspaceId));
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

        $rows = [];
        while ($row = $result->fetchAssociative()) {
            BackendUtility::workspaceOL('sys_file_reference', $row, $workspaceId);
            if (!is_array($row) || VersionState::tryFrom((int)($row['t3ver_state'] ?? 0)) === VersionState::DELETE_PLACEHOLDER) {
                continue;
            }
            $rows[] = $row;
        }
        // A workspace version may carry a sorting of its own.
        usort($rows, static fn(array $a, array $b): int => (int)$a['sorting_foreign'] <=> (int)$b['sorting_foreign']);
        return $rows;
    }
}
