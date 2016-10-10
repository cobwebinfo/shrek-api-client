<?php namespace Cobwebinfo\ShrekApiClient\Factory;

use Asika\Http\HttpClient;
use Cobwebinfo\ShrekApiClient\Http\AsikaAdapter;
use Cobwebinfo\ShrekApiClient\Support\ConfigurableMaker;

/**
 * Class AsikaAdapterFactory
 * @package Cobwebinfo\ShrekApiClient\Factory
 */
class AsikaAdapterFactory implements ConfigurableMaker
{
    /**
     * @param array $config
     * @return AsikaAdapter
     */
    public function make(array $config)
    {
        $client = new HttpClient();

        return new AsikaAdapter($client, $config);
    }
}