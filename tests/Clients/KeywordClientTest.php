<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\MemcachedStore;
use Cobwebinfo\ShrekApiClient\Clients\BaseClient;
use Cobwebinfo\ShrekApiClient\Clients\KeywordClient;
use Cobwebinfo\ShrekApiClient\DefaultApiConnector;
use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Token\AccessToken;

class KeywordClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_one_calls_get_with_correct_params()
    {
        $mock = $this->getClientMock();

        $response = new CacheableResponse(new Response());

        $mock->shouldReceive('get')
            ->once()
            ->with('keywords/' . 23, [], [])
            ->andReturn($response);

        $result =  $mock->one(23, [], []);

        $this->assertEquals($response, $result);
    }

    public function test_paginate_calls_get_with_correct_params()
    {
        $mock = $this->getClientMock();

        $response = new CacheableResponse(new Response());

        $mock->shouldReceive('get')
            ->once()
            ->with('keywords', [], ['page' => 0, 'per_page' => 1])
            ->andReturn($response);

        $result = $mock->paginate(0, 1, []);

        $this->assertEquals($response, $result);
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getClientMock()
    {
        return \Mockery::mock(KeywordClient::class)
            ->makePartial();
    }
}