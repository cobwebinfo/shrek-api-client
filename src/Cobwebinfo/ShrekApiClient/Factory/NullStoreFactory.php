<?php namespace Cobwebinfo\ShrekApiClient\Factory;

use Cobwebinfo\ShrekApiClient\Cache\NullStore;
use Cobwebinfo\ShrekApiClient\Support\Maker;

/**
 * Class MemcacheStoreFactory
 *
 * @package Cobwebinfo\ShrekApiClient\Factory
 */
class NullStoreFactory implements Maker
{

    /**
     * @return NullStore
     */
    public function make()
    {
        $store = new NullStore();

        return $store;
    }
}