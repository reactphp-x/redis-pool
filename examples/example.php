<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Redis\Pool;
use function React\Async\await;


$pool = new Pool(getenv('REDIS_URL') ?: '127.0.0.1:6379', 2, 10, 0, 0);

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

$pool->quit();