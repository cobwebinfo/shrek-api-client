<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Factory\AsikaAdapterFactory;
use Cobwebinfo\ShrekApiClient\Http\AsikaAdapter;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

class GuzzleAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function test_no_base_url_throws_exception()
    {
        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\MissingParameterException');

        $mock = $this->getMocks(array('http_client_opts' => array()));
    }

    public function test_get_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', array(), array())
            ->andReturn(true);


        $result = $mocks['adapter']->get('test');

        $this->assertEquals(true, $result);
    }

    public function test_get_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', $params, array())
            ->andReturn(true);

        $result = $mocks['adapter']->get('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_get_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', array(), $params)
            ->andReturn(true);

        $result = $mocks['adapter']->get('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_get_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'https://jsonplaceholder.typicode.com/'
        ));

        $result = $test->get('posts/1', array());

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    protected function getMocks(array $config = null)
    {
        $client = \Mockery::mock('Asika\Http\HttpClient');

        if(!$config) {
            $config = Yaml::parse(file_get_contents(__DIR__ . '/../../src/Cobwebinfo/ShrekApiClient/config.yaml'));
        }

        $adapter = new AsikaAdapter($client, $config['http_client_opts']);

        return array(
            'http' => $client,
            'adapter' => $adapter
        );
    }
}