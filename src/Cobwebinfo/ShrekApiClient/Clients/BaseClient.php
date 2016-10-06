<?php namespace Cobwebinfo\ShrekApiClient\Clients;

use Cobwebinfo\ShrekApiClient\ApiConnector;
use Cobwebinfo\ShrekApiClient\Cache\Contracts\Store;
use Cobwebinfo\ShrekApiClient\Cache\KeyGenerator;
use Cobwebinfo\ShrekApiClient\Http\CacheableResponse;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    /**
     * Class which connects to API.
     *
     * @var ApiConnector
     */
    protected $connector;

    /**
     * Class which handles caching.
     *
     * @var Store
     */
    protected $store;

    /**
     * @var KeyGenerator
     */
    protected $keyGenerator;

    /**
     * BaseClient constructor.
     *
     * @param ApiConnector $connector
     * @param Store $store
     */
    public function __construct(ApiConnector $connector, Store $store)
    {
        $this->connector = $connector;
        $this->store = $store;
        $this->keyGenerator = new KeyGenerator();
    }

    /**
     * Sends get request.
     *
     * @param $resource
     * @param array $headers
     * @param array $query
     *
     * @return ResponseInterface
     */
    public function get($resource, array $headers = [], array $query = [])
    {
        $accessToken = $this->fetchAccessToken();

        $cached = $this->fromCache($resource, $query, $accessToken);

        if ($cached) {
            return $cached;
        }

        $this->addDefaultHeaders($headers, $accessToken);

        $response = $this->connector->httpClient()->get($resource, [
            'headers' => $headers,
            'query' => $query
        ]);

        $response = $this->makeCacheable($response);

        if ($response->wasSuccessful()) {
            $key = $this->getCacheKey($resource, $query, $accessToken);
            $this->toCache($key, $response);
        }

        return $response;
    }

    /**
     * @param $headers
     * @param $accessToken
     */
    protected function addDefaultHeaders(&$headers, $accessToken)
    {
        $headers['Authorization'] = 'Bearer ' . $accessToken;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return CacheableResponse
     */
    protected function makeCacheable(ResponseInterface $response)
    {
        return new CacheableResponse($response);
    }

    /**
     * Fetches response from cache if available.
     *
     * @param $resource
     * @param array $query
     * @return mixed
     */
    protected function fromCache($resource, array $query, $accessToken)
    {
        $key = $this->getCacheKey($resource, $query, $accessToken);

        $cached = $this->store->get($key);

        return $cached;
    }

    /**
     * Adds response to cache,
     *
     * @param $key
     * @param $response
     */
    protected function toCache($key, $response)
    {
        $this->store->put($key, $response, 30);
    }

    /**
     * @param $resource
     * @param array $query
     * @param $accessToken
     * @return string
     */
    protected function getCacheKey($resource, array $query, $accessToken)
    {
        return $this->keyGenerator->generate(
            $resource,
            $query,
            $accessToken
        );
    }

    /**
     * Fetches access token from cache if one is
     * available, or from API if not.
     *
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    public function fetchAccessToken()
    {
        $token = $this->fetchAccessTokenFromCache();

        if (!$token) {
            $token = $this->connector->fetchAccessToken();

            $this->store->put('access_token', $token, 60);
        }

        return $token;
    }

    /**
     * Fetches access token from cache.
     *
     * @return bool|\League\OAuth2\Client\Token\AccessToken
     */
    protected function fetchAccessTokenFromCache()
    {
        $accessToken = $this->store->get('access_token');

        if (!$accessToken || $accessToken->hasExpired()) {
            return false;
        }

        return $accessToken;
    }
}