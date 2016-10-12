<?php namespace Cobwebinfo\ShrekApiClient\Clients;

/**
 * Class KeywordClient
 *
 * @package Cobwebinfo\ShrekApiClient\Clients
 */
class KeywordClient extends BaseClient {

    /**
     * @var string
     */
    protected $resource = 'keywords';

    /**
     * @param $id
     * @return mixed
     */
    public function one($id, $params = array())
    {
        return $this->get($this->resource .  "/" . $id, array(), $params);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function paginate($page, $take, $params = array())
    {
        $params['page'] = $page;
        $params['per_page'] = $take;

        return $this->get($this->resource, array(), $params);
    }
}