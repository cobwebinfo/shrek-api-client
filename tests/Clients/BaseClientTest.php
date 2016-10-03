<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\MemcachedStore;
use Cobwebinfo\ShrekApiClient\Clients\BaseClient;
use Cobwebinfo\ShrekApiClient\DefaultApiConnector;
use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
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

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/test", $this->getExpectedParams());

        $mocks['stub']->get('/test');
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

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/test", $params);

        $mocks['stub']->get('/test');
    }

    public function test_client_calls_correct_resource()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $this->getExpectedParams());

        $mocks['stub']->get('/keywords');
    }

    public function test_client_passes_query()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $expectedParams = $this->getExpectedParams();
        $expectedParams['query']['name'] = 'test';

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $expectedParams);

        $mocks['stub']->get('/keywords', [], ['name' => 'test']);
    }

    public function test_client_passes_custom_headers()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $expectedParams = $this->getExpectedParams();
        $expectedParams['headers']['test'] = '123';

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with("/keywords", $expectedParams);

        $mocks['stub']->get('/keywords', ['test' => '123'], []);
    }

    public function test_client_returns_http_response()
    {
        $mocks = $this->getMocks();

        $mocks['stub']->shouldReceive('fetchAccessToken')
            ->andReturn('123');

        $mocks['http']->shouldReceive('get')
            ->once()
            ->andReturn(true);

        $result = $mocks['stub']->get('/test');

        $this->assertEquals(true, $result);
    }

    public function test_parse_success_with_200_response()
    {
        $mocks = $this->getMocks();

        $response = new Response('200', [], json_encode(['data' => ['test' => '123']]));

        $this->assertEquals(['test' => '123'], $mocks['stub']->parseSuccess($response));
    }

    public function test_parse_success_with_400_response()
    {
        $mocks = $this->getMocks();

        $fourHundredExampleReturn = array (
            'error' => 'invalid_request',
            'error_description' => 'The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the "access token" parameter.'
        );

        $response = new Response('200', [], json_encode($fourHundredExampleReturn));

        $this->assertEquals(null, $mocks['stub']->parseSuccess($response));
    }

    public function test_to_json_with_null()
    {

    }

    protected function getMocks()
    {
        $httpClient = $this->getMockClient();

        $connector = $this->getMockConnector($httpClient);

        $store = $this->getMockStore();

        $stub = \Mockery::mock(StubClient::class, [$connector, $store])
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
        $client = \Mockery::mock(GuzzleAdapter::class . '[get]', [[]]);

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