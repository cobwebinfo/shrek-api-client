<?php namespace Cobwebinfo\ShrekApiClient\Http;

use Asika\Http\HttpClient;
use Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException;
use Cobwebinfo\ShrekApiClient\Exception\MissingParameterException;
use Cobwebinfo\ShrekApiClient\Http\Asika\GetRequestParams;
use Cobwebinfo\ShrekApiClient\Support\HttpRequester;

/**
 * Class GuzzleAdapter
 *
 * @package Cobwebinfo\ShrekApiClient\Http
 */
class AsikaAdapter implements HttpRequester
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $baseUrl = '';

    /**
     * AsikaAdapter constructor.
     * @param HttpClient $client
     * @param array $config
     */
    public function __construct(HttpClient $client, $config = array())
    {
        if(empty($config['base_uri'])) {
            throw new MissingParameterException('base_uri parameter must be provided.');
        }

        $this->baseUrl = $config['base_uri'];
        $this->client = $client;
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($uri, array $options = array())
    {
        $params = new GetRequestParams($options);

        return $this->client->get($this->baseUrl . $uri, $params->query, $params->headers);
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function head($uri, array $options = array())
    {
        return $this->client->head($uri, $options);
    }

    /***
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($uri, array $options = array())
    {
        return $this->client->put($uri, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($uri, array $options = array())
    {
        return $this->client->post($uri, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function patch($uri, array $options = array())
    {
        return $this->client->patch($uri, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($uri, array $options = array())
    {
        return $this->client->delete($uri, $options);
    }
}