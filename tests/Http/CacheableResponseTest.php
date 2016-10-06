<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\KeyGenerator;
use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use GuzzleHttp\Psr7\Response;

class CacheableResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_construct_populates_headers()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response());

        $expected = array (
            'Date' =>
                array (
                    0 => 'Thu, 06 Oct 2016 09:11:28 GMT',
                ),
            'Content-Type' =>
                array (
                    0 => 'text/html; charset=UTF-8',
                ),
        );

        $this->assertEquals($expected, $cacheable->headers);
    }

    public function test_construct_populates_status_code()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response());

        $this->assertEquals('200', $cacheable->statusCode);
    }

    public function test_construct_populates_reason_phrase()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response());

        $this->assertEquals('OK', $cacheable->reasonPhrase);
    }

    public function test_construct_populates_reason_phrase_error()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response('401'));

        $this->assertEquals('Unauthorized', $cacheable->reasonPhrase);
    }

    public function test_construct_populates_body()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response());

        $this->assertEquals($this->getBody(), $cacheable->body);
    }

    public function test_was_successful_returns_true_for_200_response()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response());

        $this->assertEquals(true, $cacheable->wasSuccessful());
    }

    public function test_was_successful_returns_true_for_201_response()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response('201'));

        $this->assertEquals(true, $cacheable->wasSuccessful());
    }

    public function test_was_successful_returns_false_for_401_response()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response('401'));

        $this->assertEquals(false, $cacheable->wasSuccessful());
    }

    public function test_was_successful_returns_false_for_500_response()
    {
        $cacheable = new CacheableResponse($this->getPsr7Response('500'));

        $this->assertEquals(false, $cacheable->wasSuccessful());
    }

    /**
     * @return Response
     */
    public function getPsr7Response($code = '200')
    {
        $psrResponse = new Response($code, $this->getResponseHeaders(), \GuzzleHttp\json_encode($this->getBody()));

        return $psrResponse;
    }

    /**
     * @return array
     */
    protected function getResponseHeaders()
    {
        return $responseHeaders = [
            'Date' => 'Thu, 06 Oct 2016 09:11:28 GMT',
            'Content-Type' => 'text/html; charset=UTF-8'
        ];
    }

    /**
     * @return array
     */
    protected function getBody()
    {
        return $body = [
            'test' => 123,
            'test2' => [
                '123',
                '456'
            ]
        ];
    }
}