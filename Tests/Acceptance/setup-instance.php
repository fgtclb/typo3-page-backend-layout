<?php

declare(strict_types=1);

/*
 * Sets up the TYPO3 instance the acceptance tests run against, in
 * ".Build/public/typo3temp/var/tests/acceptance". Run it through
 * "Build/Scripts/runTests.sh -s acceptance", which serves the instance afterwards.
 *
 * The backend user "admin" gets the password given in the environment variable
 * "TYPO3_ACCEPTANCE_ADMIN_PASSWORD".
 */

use SBUERK\AvailableFixturePackages;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\DataHandling\DataSet;
use TYPO3\TestingFramework\Core\Testbase;

require __DIR__ . '/../../.Build/vendor/autoload.php';

(static function (): void {
    $password = (string)getenv('TYPO3_ACCEPTANCE_ADMIN_PASSWORD');
    if ($password === '') {
        fwrite(STDERR, 'Environment variable TYPO3_ACCEPTANCE_ADMIN_PASSWORD is not set.' . PHP_EOL);
        exit(1);
    }

    $coreExtensionsToLoad = [
        'core',
        'backend',
        'extbase',
        'fluid',
        'frontend',
    ];
    $testExtensionsToLoad = [
        'fgtclb/page-backend-layout',
        'fgtclb/page-backend-layout-test',
    ];

    (new AvailableFixturePackages())->adoptFixtureExtensions();

    $testbase = new Testbase();
    $testbase->defineOriginalRootPath();
    $webRoot = $testbase->getWebRoot();
    $testbase->createDirectory($webRoot . 'typo3temp/var/tests/acceptance');
    $testbase->createDirectory($webRoot . 'typo3temp/var/transient');

    $instancePath = $webRoot . 'typo3temp/var/tests/acceptance';
    putenv('TYPO3_PATH_ROOT=' . $instancePath);
    putenv('TYPO3_PATH_APP=' . $instancePath);
    $testbase->setTypo3TestingContext();

    $testbase->removeOldInstanceIfExists($instancePath);
    $testbase->createDirectory($instancePath . '/fileadmin');
    $testbase->createDirectory($instancePath . '/typo3temp/var/transient');
    $testbase->createDirectory($instancePath . '/typo3temp/assets');
    $testbase->createDirectory($instancePath . '/typo3conf/ext');
    $testbase->setUpInstanceCoreLinks($instancePath, [], $coreExtensionsToLoad);
    $testbase->linkTestExtensionsToInstance($instancePath, $testExtensionsToLoad);

    $testbase->createDirectory(dirname($instancePath) . '/acceptance-sqlite-dbs');
    $configuration = [
        'DB' => [
            'Connections' => [
                'Default' => [
                    'driver' => 'pdo_sqlite',
                    'path' => dirname($instancePath) . '/acceptance-sqlite-dbs/test_acceptance.sqlite',
                ],
            ],
        ],
        'BE' => [
            'debug' => true,
            'installToolPassword' => '$P$notnotnotnotnotnot.validvalidva',
        ],
        'SYS' => [
            'displayErrors' => true,
            'devIPmask' => '*',
            'exceptionalErrors' => E_ALL,
            'errorHandlerErrors' => E_ALL,
            'trustedHostsPattern' => '.*',
            'encryptionKey' => 'i-am-not-a-secure-encryption-key-of-the-acceptance-tests',
        ],
    ];
    $testbase->setUpLocalConfiguration($instancePath, $configuration, []);
    $testbase->setUpPackageStates($instancePath, [], $coreExtensionsToLoad, $testExtensionsToLoad, []);
    $container = $testbase->setUpBasicTypo3Bootstrap($instancePath);
    $testbase->setUpTestDatabase($configuration['DB']['Connections']['Default']['path'], '');
    $testbase->loadExtensionTables();
    $testbase->createDatabaseStructure($container);
    restore_error_handler();

    DataSet::import(__DIR__ . '/Fixtures/database.csv');
    $passwordHash = GeneralUtility::makeInstance(PasswordHashFactory::class)
        ->getDefaultHashInstance('BE')
        ->getHashedPassword($password);
    GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable('be_users')
        ->insert('be_users', [
            'uid' => 1,
            'pid' => 0,
            'username' => 'admin',
            'password' => $passwordHash,
            'admin' => 1,
        ]);

    $testbase->createDirectory($instancePath . '/typo3conf/sites/main');
    file_put_contents(
        $instancePath . '/typo3conf/sites/main/config.yaml',
        file_get_contents(__DIR__ . '/Fixtures/site.yaml')
    );
})();
