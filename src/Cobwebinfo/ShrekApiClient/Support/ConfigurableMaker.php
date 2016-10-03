<?php namespace Cobwebinfo\ShrekApiClient\Support;

/**
 * Interface Maker
 * @package Cobwebinfo\ShrekApiClient\Support
 */
interface ConfigurableMaker
{
    /**
     * Should return an object.
     *
     * @return mixed
     */
    public function make(array $config);
}