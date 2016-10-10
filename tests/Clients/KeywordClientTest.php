<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use Asika\Http\Response;
use League\OAuth2\Client\Token\AccessToken;

class KeywordClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_one_calls_get_with_correct_params()
    {
        $mock = $this->getClientMock();

        $response = new CacheableResponse(new Response());

        $mock->shouldReceive('get')
            ->once()
            ->with('keywords/' . 23, array(), array())
            ->andReturn($response);

        $result =  $mock->one(23, array(), array());

        $this->assertEquals($response, $result);
    }

    public function test_paginate_calls_get_with_correct_params()
    {
        $mock = $this->getClientMock();

        $response = new CacheableResponse(new Response());

        $mock->shouldReceive('get')
            ->once()
            ->with('keywords/', array(), array('page' => 0, 'per_page' => 1))
            ->andReturn($response);

        $result = $mock->paginate(0, 1, array());

        $this->assertEquals($response, $result);
    }

    /**
     * @return \Mockery\Mock
     */
    protected function getClientMock()
    {
        return \Mockery::mock('Cobwebinfo\ShrekApiClient\Clients\KeywordClient')
            ->makePartial();
    }
}