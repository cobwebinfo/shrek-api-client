<?php namespace Cobwebinfo\ShrekApiClient\Http\Asika;

/**
 * Class GetRequestParams
 */
class QueryStringRequestParams extends GetRequestParams
{
    /**
     * @return string
     */
    public function getQueryString()
    {
        return "?" . http_build_query($this->query);
    }

    /**
     * @return bool
     */
    public function hasQuery() {
        return (bool) count($this->query);
    }
}