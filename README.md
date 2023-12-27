# reactphp-framework-redis-pool

## install
```
composer require reactphp-framework/redis-pool -vvv
```

## Usage

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\Redis\Pool;
use function React\Async\await;


$pool = new Pool(getenv('REDIS_URL') ?: '127.0.0.1:6379', [
    'min_connections' => 2, // 2 connection
    'max_connections' => 10, // 10 connection
    'max_wait_queue' => 100, // how many sql in queue
    'wait_timeout' => 5, // wait time include response time
    'keep_alive' => 60
]);

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