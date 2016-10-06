<?php namespace Cobwebinfo\ShrekApiClient\Factory;

use Cobwebinfo\ShrekApiClient\Cache\ApcStore;
use Cobwebinfo\ShrekApiClient\Cache\ApcWrapper;
use Cobwebinfo\ShrekApiClient\Support\Maker;

/**
 * Class MemcacheStoreFactory
 *
 * @package Cobwebinfo\ShrekApiClient\Factory
 */
class ApcStoreFactory implements Maker
{
    /**
     * @return \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore
     */
    public function make()
    {
        $wrapper = new ApcWrapper();

        $store = new ApcStore($wrapper);

        return $store;
    }
}