<?php namespace Cobwebinfo\ShrekApiClient\Http\Asika;

/**
 * Class GetRequestParams
 */
class GetRequestParams
{
    /**
     * @var array
     */
    public $headers = array();

    /**
     * @var array
     */
    public $query = array();

    /**
     * GetRequestParams constructor.
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        $this->populateHeaders($params);
        $this->populateQuery($params);
    }

    /**
     * @param $params
     */
    protected function populateHeaders($params)
    {
        if(empty($params['headers'])) {
            return;
        }

        foreach($params['headers'] as $name => $header) {
            $this->headers[$name] = $header;
        }
    }

    /**
     * @param $params
     */
    protected function populateQuery($params)
    {
        if(empty($params['query'])) {
            return;
        }

        foreach($params['query'] as $name=>$query) {
            $this->query[$name] = $query;
        }
    }

}