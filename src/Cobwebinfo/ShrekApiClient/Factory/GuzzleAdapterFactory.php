<?php namespace Cobwebinfo\ShrekApiClient\Factory;

use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
use Cobwebinfo\ShrekApiClient\Support\ConfigurableMaker;

/**
 * Class GuzzleAdapterFactory
 * @package Cobwebinfo\ShrekApiClient\Factory
 */
class GuzzleAdapterFactory implements ConfigurableMaker
{
    /**
     * @param array $config
     * @return GuzzleAdapter
     */
    public function make(array $config)
    {
        return new GuzzleAdapter($config);
    }
}