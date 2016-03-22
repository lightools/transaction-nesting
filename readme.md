## Introduction

This library allows you to nest database transactions over dibi connection.

## Installation

```sh
$ composer require lightools/transaction-nesting
```

## Simple usage

TransactionManager will begin/commit/rollback database transaction only if there is no other transaction running.
So if you try to start new transaction when some transaction is active, it will not fail with error ```There is already an active transaction```.
This means you can nest transactions safely and TransactionManager will make sure that only the outer transaction will be performed.

```php
$dibi = new Dibi\Connection($config);
$manager = new Lightools\TransactionNesting\TransactionManager($dibi);

$manager->transactional(function () {
    // your logic
});
```

Of course, this will break if you perform some query causing implicit commit (for example ALTER TABLE on MySQL).

## How to run tests

```sh
$ vendor/bin/tester -c tests/php.ini -d extension_dir=ext tests
```
