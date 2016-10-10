<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\MemcachedStore;
use Cobwebinfo\ShrekApiClient\Clients\BaseClient;
use Cobwebinfo\ShrekApiClient\DefaultApiConnector;
use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Token\AccessToken;

class StubClient extends BaseClient {}

class BaseClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_client_adds_auth_header()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/test", $this->getExpectedParams())
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/test');

        $this->assertEquals(new CacheableResponse($response), $result);
    }

    public function test_client_uses_cached_token_for_auth_header()
    {
        $mocks = $this->getMocks();

        $token = new AccessToken(['access_token' => 456, 'expires' => '123']);

        $mocks['store']->shouldReceive('get')
            ->with('access_token')
            ->andReturn($token);

        $params = $this->getExpectedParams();
        $params['headers']['Authorization'] = 'Bearer 456';

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/test", $params)
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/test');

        $this->assertEquals(new CacheableResponse($response), $result);
    }

    public function test_client_calls_correct_resource()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $this->getExpectedParams())
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/keywords');

        $this->assertEquals(new CacheableResponse($response), $result);
    }

    public function test_client_returns_from_cache_if_available()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $response = new Response(200, [] ,json_encode(['test' => 123]));
        $cacheableResponse = new CacheableResponse($response);

        $mocks['store']->shouldReceive('get')
            ->once()
            ->andReturn($cacheableResponse);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/keywords', [], ['name' => 'test']);

        $this->assertEquals($cacheableResponse, $result);
    }

    public function test_client_passes_query()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $expectedParams = $this->getExpectedParams();
        $expectedParams['query']['name'] = 'test';

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $expectedParams)
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/keywords', [], ['name' => 'test']);

        $this->assertEquals(new CacheableResponse($response), $result);
    }

    public function test_client_passes_custom_headers()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $expectedParams = $this->getExpectedParams();
        $expectedParams['headers']['test'] = '123';

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $expectedParams)
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result =  $mocks['stub']->get('/keywords', ['test' => '123'], []);

        $this->assertEquals(new CacheableResponse($response), $result);
    }

    public function test_client_returns_cacheable_response()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $response = new Response();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->andReturn($response);

        $mocks['stub']->shouldReceive('fromCache')
            ->andReturn(null);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/test');

        $this->assertInstanceOf(CacheableResponse::class, $result);
    }

    protected function getMocks()
    {
        $httpClient = $this->getMockClient();

        $connector = $this->getMockConnector($httpClient);

        $store = $this->getMockStore();

        $stub = \Mockery::mock(StubClient::class, [$connector, $store])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        return [
            'stub' => $stub,
            'store' => $store,
            'http' => $httpClient,
            'connector' => $connector
        ];
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getMockClient()
    {
        $client = \Mockery::mock(GuzzleAdapter::class . '[get]', new Client());

        return $client;
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getMockConnector($http)
    {
        $connector = \Mockery::mock(DefaultApiConnector::class)
            ->makePartial();

        $connector->shouldReceive('httpClient')
            ->andReturn($http);

        return $connector;
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getMockStore()
    {
        $store = \Mockery::mock(MemcachedStore::class)
            ->makePartial();

        return $store;
    }

    /**
     * @return array
     */
    protected function getExpectedParams()
    {
        $params = array (
            'headers' =>
                array (
                    'Authorization' => 'Bearer 123',
                ),
            'query' => array ()
        );

        return $params;
    }
}