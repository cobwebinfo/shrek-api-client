<?php namespace Cobwebinfo\ShrekApiClient;

use Cobwebinfo\ShrekApiClient\Cache\Contracts\Store;
use Cobwebinfo\ShrekApiClient\Factory\MemcacheStoreFactory;
use Cobwebinfo\ShrekApiClient\Exception\MethodNotFoundException;
use Cobwebinfo\ShrekApiClient\Support\ConfigurableMaker;
use Cobwebinfo\ShrekApiClient\Support\HttpRequester;
use Cobwebinfo\ShrekApiClient\Support\Maker;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ShrekServiceProvider
 *
 * @package Cobwebinfo\ShrekApiClient
 */
class ShrekServiceProvider
{
    /**
     * @var Factory\MemcacheStoreFactory
     */
    protected $store;

    /**
     * @var DefaultApiConnector
     */
    protected $connector;

    /**
     * @var
     */
    public $config;

    /**
     * Maps handles to store factories.
     *
     * @var array
     */
    protected $nativeStores = array(
        'apc' => 'Cobwebinfo\ShrekApiClient\Factory\ApcStoreFactory',
        'memcache' => 'Cobwebinfo\ShrekApiClient\Factory\MemcacheStoreFactory',
        'none' => 'Cobwebinfo\ShrekApiClient\Factory\NullStoreFactory'
    );

    /**
     * Maps handles to client factories.
     *
     * @var array
     */
    protected $nativeClients = array(
        'asika' => 'Cobwebinfo\ShrekApiClient\Factory\AsikaAdapterFactory',
    );

    /**
     * ShrekServiceProvider constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $this->mergeConfig($this->getDefaultConfig(), $config);

        $this->store = $this->buildStore($this->config['cache_driver']);

        $httpClient = $this->buildHttpClient();

        $connector = new \Cobwebinfo\ShrekApiClient\DefaultApiConnector($this->config, $httpClient);

        $this->connector = $connector;
    }

    /**
     * @param $handle
     * @return mixed
     * @throws MethodNotFoundException
     */
    protected function buildStore($handle)
    {
        $factory = $this->resolveFactory($handle, $this->nativeStores);

        if (!$factory instanceof Maker) {
            throw new MethodNotFoundException('Store factory must implement "make" method.');
        }

        $obj = $factory->make();

        if ($obj instanceof Store) {
            return $obj;
        } else {
            throw new MethodNotFoundException('Object returned by factory must implement Cobwebinfo\ShrekApiClient\Cache\Contracts\Store');
        }
    }

    /**
     * @return mixed
     */
    protected function buildHttpClient()
    {
        $client =  $this->config['http_client'];

        $clientOpts = $this->config['http_client_opts'];

        return $this->buildClient($client, $clientOpts);
    }

    /**
     * @param $handle
     * @param $opts
     * @return mixed
     * @throws MethodNotFoundException
     */
    protected function buildClient($handle, $opts)
    {
        $factory = $this->resolveFactory($handle, $this->nativeClients);

        if (!$factory instanceof ConfigurableMaker) {
            throw new MethodNotFoundException('Client factory must implement Cobwebinfo\ShrekApiClient\Support\ConfigurableMaker');
        }

        $client = $factory->make($opts);

        if ($client instanceof HttpRequester) {
            return $client;
        } else {
            throw new MethodNotFoundException('Object returned by factory must implement Cobwebinfo\ShrekApiClient\Support\HttpRequester');
        }
    }

    /**
     * @param $handle
     * @return Maker
     */
    protected function resolveFactory($handle, $allowed)
    {
        $factory = null;

        if (array_key_exists($handle, $allowed)) {
            $factory = new $allowed[$handle];
        } else {
            $factory = new $handle;
        }

        return $factory;
    }

    /**
     * Merges user config into default. Using user
     * config as preference if there are collisions.
     *
     * @param $defaultConfig
     * @param $customConfig
     * @return mixed
     */
    protected function mergeConfig($defaultConfig, $customConfig)
    {
        return $customConfig + $defaultConfig;
    }

    /**
     * Parses default config.
     *
     * @return mixed
     */
    protected function getDefaultConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/config.yaml'));
    }

    /**
     * @return Clients\KeywordClient
     */
    public function getKeywordClient()
    {
        return new \Cobwebinfo\ShrekApiClient\Clients\KeywordClient($this->connector, $this->store);
    }

    /**
     * @return Factory\MemcacheStoreFactory
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @return DefaultApiConnector
     */
    public function getConnector()
    {
        return $this->connector;
    }
}