<?php

namespace ReactphpX\Redis;

use ReactphpX\Pool\AbstractConnectionPool;
use Clue\React\Redis\RedisClient;
use React\Promise\PromiseInterface;

class Pool extends AbstractConnectionPool
{
    protected function createConnection()
    {
        $redis = new RedisClient($this->uri);
        $redis->on('close', function () use ($redis): void {
            if ($this->pool->contains($redis)) {
                $this->pool->detach($redis);
            }
            $this->currentConnections--;
        });
        $this->currentConnections++;
        return $redis;
    }

    public function __call($method, $args): PromiseInterface
    {
        return $this->getConnection()->then(function ($connection) use ($method, $args) {
            return $connection->$method(...$args)->then(function ($result) use ($connection) {
                $this->releaseConnection($connection);
                return $result;
            }, function ($error) use ($connection) {
                $this->releaseConnection($connection);
                throw $error;
            });
        });
    }
}
