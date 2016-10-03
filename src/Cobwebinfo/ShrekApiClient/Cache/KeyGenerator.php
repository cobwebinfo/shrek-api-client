<?php namespace Cobwebinfo\ShrekApiClient\Cache;

/**
 * Class KeyGenerator
 *
 * Generates a deterministic key based on
 * $query, which can be used for caching.
 *
 * @package Cobwebinfo\ShrekApiClient\Cache
 */
class KeyGenerator
{
    /**
     * @param $resource
     * @param array $query
     * @param $accessToken
     * @return string
     */
    public function generate($resource, array $query, $accessToken)
    {
        $query['client_access_token'] = $accessToken;

        return $this->generatePrefix($resource). md5(serialize($query));
    }

    /**
     * Generates prefix from resource name.
     *
     * @param $resource
     * @return string
     */
    protected function generatePrefix($resource)
    {
        return $resource . "_";
    }
}