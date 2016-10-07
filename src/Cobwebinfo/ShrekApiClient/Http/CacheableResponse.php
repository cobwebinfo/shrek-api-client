<?php namespace Cobwebinfo\ShrekApiClient\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Class CacheableResponse
 *
 * @package Cobwebinfo\ShrekApiClient\Http
 */
class CacheableResponse
{
    /**
     * @var array
     */
    public $headers = array();

    /**
     * @var
     */
    public $statusCode;

    /**
     * @var string
     */
    public $reasonPhrase;

    /**
     * @var string
     */
    private $body;

    /**
     * CacheableResponse constructor.
     * @param ResponseInterface $psrResponse
     */
    public function __construct(ResponseInterface $psrResponse)
    {
        $this->headers = $psrResponse->getHeaders();
        $this->statusCode = $psrResponse->getStatusCode();
        $this->reasonPhrase = $psrResponse->getReasonPhrase();
        $this->body = (string) $psrResponse->getBody();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'body') {
           return json_decode($this->body, true);
        }
    }

    /**
     * Returns true if response code
     * indicates success.
     *
     * @return bool
     */
    public function wasSuccessful()
    {
        $firstNum =  (int) substr($this->statusCode, 0, 1);

        if ($firstNum == 4 || $firstNum == 5) {
           return false;
        }

        return true;
    }
}