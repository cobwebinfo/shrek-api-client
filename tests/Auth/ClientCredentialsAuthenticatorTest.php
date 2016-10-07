<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Auth\ClientCredentialsParameters;
use Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException;
use Symfony\Component\Yaml\Yaml;

class ClientCredentialsAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_invalid_client_id_throws_exception()
    {
        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException');

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../src/Cobwebinfo/ShrekApiClient/config.yaml'));

        $config['client_id'] = null;

        $instance = new ClientCredentialsParameters($config);
    }

    public function test_invalid_client_secret_throws_exception()
    {
        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException');

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../src/Cobwebinfo/ShrekApiClient/config.yaml'));

        $config['client_secret'] = null;

        $instance = new ClientCredentialsParameters($config);
    }

    public function test_cast_to_array_has_necessary_properties()
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../src/Cobwebinfo/ShrekApiClient/config.yaml'));

        $instance = new ClientCredentialsParameters($config);

        $params = $instance->toArray();

        $this->assertEquals('YOUR_ID', $params['clientId']);
        $this->assertEquals('YOUR_SECRET', $params['clientSecret']);
        $this->assertEquals('', $params['redirectUri']);
        $this->assertEquals('', $params['urlAuthorize']);
        $this->assertEquals('http://shrek-api.cobwebinfo.com/v1/oauth/access_token', $params['urlAccessToken']);
        $this->assertEquals('', $params['urlResourceOwnerDetails']);
    }
}