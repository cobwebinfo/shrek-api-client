<?php namespace Cobwebinfo\ShrekApiClient\Tests;

class CacheMemcachedStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReturnsNullWhenNotFound()
    {
        $memcache = $this->getMock('StdClass', array('get', 'getResultCode'));
        $memcache->expects($this->once())->method('get')->with($this->equalTo('foo:bar'))->will($this->returnValue(null));
        $memcache->expects($this->once())->method('getResultCode')->will($this->returnValue(1));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache, 'foo');
        $this->assertNull($store->get('bar'));
    }

    public function testMemcacheValueIsReturned()
    {
        $memcache = $this->getMock('StdClass', array('get', 'getResultCode'));
        $memcache->expects($this->once())->method('get')->will($this->returnValue('bar'));
        $memcache->expects($this->once())->method('getResultCode')->will($this->returnValue(0));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache);
        $this->assertEquals('bar', $store->get('foo'));
    }

    public function testMemcacheGetMultiValuesAreReturnedWithCorrectKeys()
    {
        if (! class_exists('Memcached')) {
            $this->markTestSkipped('Memcached module not installed');
        }
        $memcache = $this->getMock('StdClass', array('getMulti', 'getResultCode'));
        $memcache->expects($this->once())->method('getMulti')->with(
            array('foo:foo', 'foo:bar', 'foo:baz')
        )->will($this->returnValue(array(
            'fizz', 'buzz', 'norf',
        )));
        $memcache->expects($this->once())->method('getResultCode')->will($this->returnValue(0));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache, 'foo');
        $this->assertEquals(array(
            'foo'   => 'fizz',
            'bar'   => 'buzz',
            'baz'   => 'norf',
        ), $store->many(array(
            'foo', 'bar', 'baz',
        )));
    }

    public function testSetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcached', array('set'));
        $memcache->expects($this->once())->method('set')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache);
        $store->put('foo', 'bar', 1);
    }

    public function testStoreItemForeverProperlyCallsMemcached()
    {
        $memcache = $this->getMock('Memcached', array('set'));
        $memcache->expects($this->once())->method('set')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(0));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache);
        $store->forever('foo', 'bar');
    }

    public function testForgetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcached', array('delete'));
        $memcache->expects($this->once())->method('delete')->with($this->equalTo('foo'));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore($memcache);
        $store->forget('foo');
    }

    public function testGetAndSetPrefix()
    {
        $store = new \Cobwebinfo\ShrekApiClient\Cache\MemcachedStore(new \Memcached(), 'bar');
        $this->assertEquals('bar:', $store->getPrefix());
        $store->setPrefix('foo');
        $this->assertEquals('foo:', $store->getPrefix());
        $store->setPrefix(null);
        $this->assertEmpty($store->getPrefix());
    }
}