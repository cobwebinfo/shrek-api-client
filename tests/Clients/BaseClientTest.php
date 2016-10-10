<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Asika\Http\HttpClient;
use Cobwebinfo\ShrekApiClient\Clients\BaseClient;
use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use Asika\Http\Response;
use Cobwebinfo\ShrekApiClient\Auth\AccessToken;
use Guzzle\Http\Client;

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

        $token = new AccessToken(array('access_token' => 456, 'expires' => '123'));

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

        $response = new Response(json_encode(array('test' => 123), 200));
        $cacheableResponse = new CacheableResponse($response);

        $mocks['store']->shouldReceive('get')
            ->once()
            ->andReturn($cacheableResponse);

        $mocks['stub']->shouldReceive('toCache');

        $result = $mocks['stub']->get('/keywords', array(), array('name' => 'test'));

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

        $result = $mocks['stub']->get('/keywords', array(), array('name' => 'test'));

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

        $result =  $mocks['stub']->get('/keywords', array('test' => '123'), array());

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

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Http\CacheableResponse', $result);
    }

    protected function getMocks()
    {
        $httpClient = $this->getMockClient();

        $connector = $this->getMockConnector($httpClient);

        $store = $this->getMockStore();

        $stub = \Mockery::mock('Cobwebinfo\ShrekApiClient\Tests\StubClient', array($connector, $store))
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        return array(
            'stub' => $stub,
            'store' => $store,
            'http' => $httpClient,
            'connector' => $connector
        );
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getMockClient()
    {
        $client = \Mockery::mock('Cobwebinfo\ShrekApiClient\Http\AsikaAdapter' . '[get]', array(
            new HttpClient(),
            array('base_uri' => 'test')
        ));

        return $client;
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getMockConnector($http)
    {
        $connector = \Mockery::mock('Cobwebinfo\ShrekApiClient\DefaultApiConnector')
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
        $store = \Mockery::mock('Cobwebinfo\ShrekApiClient\Cache\MemcachedStore')
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