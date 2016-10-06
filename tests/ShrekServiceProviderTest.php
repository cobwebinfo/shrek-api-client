<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\ApcStore;
use Cobwebinfo\ShrekApiClient\Cache\MemcachedStore;
use Cobwebinfo\ShrekApiClient\Cache\NullStore;
use Cobwebinfo\ShrekApiClient\Factory\ApcStoreFactory;
use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
use Cobwebinfo\ShrekApiClient\ShrekServiceProvider;
use Cobwebinfo\ShrekApiClient\Support\ConfigurableMaker;
use Cobwebinfo\ShrekApiClient\Support\HttpRequester;
use Cobwebinfo\ShrekApiClient\Support\Maker;

/**
 * Class ShrekServiceProviderTest
 * 
 * @package Cobwebinfo\ShrekApiClient\Tests
 */
class ShrekServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_default_config_is_loaded()
    {
        $instance = $this->getMockInstance([]);

        $config = $instance->config;

        $this->assertEquals([
            'client_id'=> 'YOUR_ID',
            'client_secret'=> 'YOUR_SECRET',
            'cache_driver'=> 'memcache',
            'auth_uri' => "http://shrek-api.cobwebinfo.com/v1/oauth/access_token",
            'http_client' => 'guzzle',
            'http_client_opts'=> [
                'base_uri' => "http://shrek-api.cobwebinfo.com/v1/"
            ],
        ], $config);
    }

    public function test_default_config_can_be_customized()
    {
        $instance = $this->getMockInstance([
            'client_id' => 2,
            'http_client_opts'=> [
                'base_uri' => "http://sources_api.local/v2/"
            ],
        ]);

        $config = $instance->config;

        $this->assertEquals([
            'client_id'=> 2,
            'client_secret'=> 'YOUR_SECRET',
            'cache_driver'=>'memcache',
            'auth_uri' => "http://shrek-api.cobwebinfo.com/v1/oauth/access_token",
            'http_client' => 'guzzle',
            'http_client_opts'=> [
                'base_uri' => "http://sources_api.local/v2/"
            ],
        ], $config);
    }

    public function test_default_uses_memcache()
    {
        $instance = $this->getMockInstance([]);

        $this->assertInstanceOf(MemcachedStore::class, $instance->getStore());
    }

    public function test_non_default_option()
    {
        $instance = $this->getMockInstance([
            'cache_driver' => 'none'
        ]);

        $this->assertInstanceOf(NullStore::class, $instance->getStore());
    }

    public function test_custom_option()
    {
        $instance = $this->getMockInstance([
            'cache_driver' => MockFactory::class
        ]);

        $this->assertInstanceOf(NullStore::class, $instance->getStore());
    }

    public function test_apc_store()
    {
        $instance = $this->getMockInstance([
            'cache_driver' => ApcStoreFactory::class
        ]);

        $this->assertInstanceOf(ApcStore::class, $instance->getStore());
    }

    public function test_custom_invalid_interface_throws_exception()
    {
        $this->setExpectedException(\Cobwebinfo\ShrekApiClient\Exception\MethodNotFoundException::class);

        $instance = $this->getMockInstance([
            'cache_driver' => InvalidMockFactory::class
        ]);
    }

    public function test_custom_invalid_return_value_throws_exception()
    {
        $this->setExpectedException(\Cobwebinfo\ShrekApiClient\Exception\MethodNotFoundException::class);

        $instance = $this->getMockInstance([
            'cache_driver' => InvalidMockFactory2::class
        ]);
    }

    public function test_default_http_client()
    {
        $instance = $this->getMockInstance([]);

        $this->assertInstanceOf(GuzzleAdapter::class, $instance->getConnector()->httpClient());
    }

    public function test_custom_http_client()
    {
        $instance = $this->getMockInstance([
            'http_client' => MockHttpClientFactory::class
        ]);

        $this->assertInstanceOf(MockHttpClient::class, $instance->getConnector()->httpClient());
    }

    public function test_custom_http_opts()
    {
        $instance = $this->getMockInstance([
            'http_client' => MockHttpClientFactory::class,
            'http_client_opts' => [
                'base_uri' => 'http://CONFIGURED.com'
            ]
        ]);

        $this->assertInstanceOf(MockHttpClient::class, $instance->getConnector()->httpClient());

       $this->assertEquals('http://CONFIGURED.com', $instance->getConnector()->httpClient()->get('', []));

    }

    protected function getMockInstance($config = [])
    {
        return new ShrekServiceProvider($config);
    }
}


class MockFactory implements Maker{

    public function make()
    {
        return new NullStore();
    }
}

class InvalidMockFactory {
}

class InvalidMockFactory2 implements Maker{
    public function make()
    {
        return new \stdClass();
    }
}

class MockHttpClient implements HttpRequester {

    public function __construct($opts)
    {
        $this->ops = $opts;
    }

    public function get($uri, array $options = [])
    {
        return $this->ops['base_uri'];
    }

    public function head($uri, array $options = [])
    {
        // TODO: Implement head() method.
    }

    public function put($uri, array $options = [])
    {
        // TODO: Implement put() method.
    }

    public function post($uri, array $options = [])
    {
        // TODO: Implement post() method.
    }

    public function patch($uri, array $options = [])
    {
        // TODO: Implement patch() method.
    }

    public function delete($uri, array $options = [])
    {
        // TODO: Implement delete() method.
    }

}
class MockHttpClientFactory implements ConfigurableMaker {
    public function make(array $config) {
        return new MockHttpClient($config);
    }
}