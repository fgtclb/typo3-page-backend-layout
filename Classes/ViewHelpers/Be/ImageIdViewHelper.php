<?php

declare(strict_types=1);

namespace FGTCLB\PageBackendLayout\ViewHelpers\Be;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImageIdViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('table', 'string', 'The table', true);
        $this->registerArgument('field', 'string', 'Field matching', true);
        $this->registerArgument('uid', 'int', 'Integer of current record', true);
        $this->registerArgument('returnFirst', 'bool', 'Whether to return only the frist value', false, true);
        $this->registerArgument('as', 'string', 'Variable name to return', false, 'image');
    }

    /**
     * @throws FileDoesNotExistException
     * @throws Exception
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
        $renderingContext = $this->renderingContext;
        $renderChildrenClosure = $this->renderChildrenClosure;

        $request = $renderingContext->getRequest();
        $isBackendRequest = $request->getAttribute('applicationType')
            && ApplicationType::fromRequest($request)->isBackend();
        if (!$isBackendRequest) {
            throw new \RuntimeException(
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

        $db = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');
        $statement = $db
            ->select(
                ['*'],
                'sys_file_reference',
                [
                    'tablenames' => $table,
                    'fieldname' => $field,
                    'uid_foreign' => $uid,
                ]
            );
        if ($returnFirst) {
            $result = $statement->fetchAssociative();
            if ($result !== false) {
                $value = new FileReference($result);
            } else {
                $value = false;
            }
        } else {
            $result = $statement->fetchAllAssociative();
            $value = [];
            foreach ($result as $line) {
                $value[] = new FileReference($line);
            }
        }

        $templateVariableContainer->add($varName, $value);
        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($varName);
        return $output;
    }
}
