# reactphp-framework-redis-pool

## install
```
composer require reactphp-x/redis-pool -vvv
```

## Usage

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Redis\Pool;
use function React\Async\await;


$pool = new Pool(
    uri: getenv('REDIS_URL') ?: '127.0.0.1:6379',
    minConnections: 2,
    maxConnections: 10,
    waitQueue: 100,
    waitTimeout: 0,
);

// see https://github.com/clue/reactphp-redis?tab=readme-ov-file#quickstart-example
await($pool->set('greeting', 'Hello world'));
await($pool->append('greeting', '!'));

$pool->get('greeting')->then(function (string $greeting) {
    // Hello world!
    echo $greeting . PHP_EOL;
});

$pool->incr('invocation')->then(function (int $n) {
    echo 'This is invocation #' . $n . PHP_EOL;
});

```
