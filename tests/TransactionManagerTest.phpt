<?php

namespace Lightools\Tests;

use Dibi\Connection;
use ErrorException;
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

    /**
     * @var Connection
     */
    private $connection;

    protected function setUp() {
        parent::setUp();

        $this->connection = new Connection([
            'driver' => 'sqlite3',
            'database' => sys_get_temp_dir() . '/transaction-manager-test-' . uniqid() . '.sq3',
        ]);
        $this->connection->query('CREATE TABLE [test] ([id] INT)');
    }

    public function testNesting() {
        $manager = new TransactionManager($this->connection);

        Assert::noError(function () use ($manager) {
            $manager->transactional(function () use ($manager) {
                $manager->transactional(function () {
                    // it works!
                });
            });
        });
    }

    public function testCommit() {
        $manager = new TransactionManager($this->connection);

        $manager->transactional(function () {
            $this->connection->insert('test', ['id' => 1])->execute();
        });

        Assert::truthy($this->connection->fetch('SELECT [id] FROM [test]'));
    }

    public function testRollback() {
        $manager = new TransactionManager($this->connection);

        Assert::exception(function () use ($manager) {
            $manager->transactional(function () {
                $this->connection->insert('test', ['id' => 1])->execute();
                throw new ErrorException;
            });
        }, ErrorException::class);

        Assert::falsey($this->connection->fetchSingle('SELECT [id] FROM [test]'));
    }

}

(new TransactionManagerTest)->run();
