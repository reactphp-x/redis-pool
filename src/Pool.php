<?php

namespace ReactphpX\Redis;

use ReactphpX\Pool\AbstractConnectionPool;
use React\Socket\ConnectorInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Clue\React\Redis\RedisClient;
use React\Promise\PromiseInterface;

class Pool extends AbstractConnectionPool
{
    protected $url;
    protected $connector;
    protected $loop;

    /**
     * @param string $url
     * @param ?ConnectorInterface $connector
     * @param ?LoopInterface $loop
     */
    public function __construct($url, $config = [], ConnectorInterface $connector = null, LoopInterface $loop = null)
    {
        $this->url = $url;
        $this->connector = $connector;
        $this->loop = $loop ?: Loop::get();
        parent::__construct($config, $loop);
    }

    protected function createConnection()
    {
        return new RedisClient($this->url, $this->connector, $this->loop);
    }

    public function __call($method, $args):PromiseInterface
    {
        return $this->getConnection()->then(function ($connection) use ($method, $args) {
            return $connection->$method(...$args)->then(function ($result) use ($connection) {
                $this->releaseConnection($connection);
                return $result;
            }, function ($error) use ($connection) {
                $this->_ping($connection);
                throw $error;
            })->catch(function ($error) use ($connection) {
                $this->_ping($connection);
                throw $error;
            });
        }, function ($error) {
            throw $error;
        })->catch(function ($error) {
            throw $error;
        });
    }


}
