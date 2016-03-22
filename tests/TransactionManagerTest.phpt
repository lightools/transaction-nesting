<?php

namespace Tests;

use Dibi\Connection;
use Lightools\TransactionNesting\TransactionManager;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

/**
 * @testCase
 * @author Jan Nedbal
 */
class TransactionManagerTest extends TestCase {

    public function testNesting() {

        $connection = new Connection(array(
            'driver' => 'sqlite3',
            'database' => sys_get_temp_dir() . '/transaction-manager-test.sq3',
        ));
        $manager = new TransactionManager($connection);

        Assert::noError(function () use ($manager) {
            $manager->transactional(function () use ($manager) {
                $manager->transactional(function () {
                    // it works!
                });
            });
        });
    }

}

(new TransactionManagerTest)->run();
