<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\NullStore;

class NullStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_returns_false()
    {
        $store = new NullStore();

        $store->put('test', 1, 60);

        $this->assertEquals(false, $store->get('test'));
    }

    public function test_many_returns_empty()
    {
        $store = new NullStore();

        $store->putMany(array('test' => 1, 'xyz' => 2), 60);

        $this->assertEquals(array(), $store->many(array('test', 'xyz')));
    }

    public function test_prefix_returns_empty()
    {
        $store = new NullStore();

        $this->assertEquals('', $store->getPrefix());
    }
}