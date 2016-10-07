<?php namespace Cobwebinfo\ShrekApiClient\Factory;

use Cobwebinfo\ShrekApiClient\Support\Maker;

/**
 * Class MemcacheStoreFactory
 *
 * @package Cobwebinfo\ShrekApiClient\Factory
 */
class MemcacheStoreFactory implements Maker
{
    /**
     * @var array
     */
    protected $cacheServers =  array(
        array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 1
        )
    );

    /**
     * @return \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore
     */
    public function make()
    {
        $connection = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedConnector();

        $connection = $connection->connect($this->cacheServers);

        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($connection, 'cobweb_');

        return $store;
    }

    /**
     * Sets memcache server details which
     * will be used to open a connection.
     *
     * @param array[array] $servers
     */
    public function setCacheServers(array $servers)
    {
        $this->cacheServers = $servers;
    }
}