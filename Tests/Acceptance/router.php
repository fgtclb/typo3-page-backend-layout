<?php

declare(strict_types=1);

/*
 * Router of the PHP built-in web server serving the acceptance instance: existing files
 * are delivered as they are, every other request goes through the TYPO3 entry point,
 * as the rewrite rule of a real web server would do it.
 */

$path = (string)parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file($_SERVER['DOCUMENT_ROOT'] . $path)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';

require $_SERVER['SCRIPT_FILENAME'];
