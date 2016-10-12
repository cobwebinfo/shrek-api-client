<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Factory\AsikaAdapterFactory;
use Cobwebinfo\ShrekApiClient\Factory\GuzzleAdapterFactory;
use Cobwebinfo\ShrekApiClient\Http\AsikaAdapter;
use Cobwebinfo\ShrekApiClient\Http\GuzzleAdapter;
use Cobwebinfo\ShrekApiClient\Query\KeywordQuery;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

class GuzzleAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Guzzle adds base url automatically. Dont want to add it twice.
     */
    public function test_get_excludes_base_url()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('test', array())
            ->andReturn(true);


        $result = $mocks['adapter']->get('test');

        $this->assertEquals(true, $result);
    }


    public function test_get_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->get('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_get_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'headers' => array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('get')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->get('test',$params);

        $this->assertEquals(true, $result);
    }

    public function test_get_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->get('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_guzzle_handles_white_space()
    {
        $test = new GuzzleAdapterFactory();

        $query = new KeywordQuery([]);

        $query->where('name', 'hello there ');

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->get('posts', ['query' => $query->toArray()]);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_head_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('head')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->head('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_head_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'headers' =>  array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('head')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->head('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_head_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->head('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_put_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params =  array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('put')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->put('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_put_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'headers' => array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('put')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->put('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_put_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->put('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_post_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('post')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->post('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_post_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'headers' => array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('post')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->post('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_post_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->post('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_patch_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('patch')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->patch('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_patch_adds_headers()
    {
        $mocks = $this->getMocks();

        $params =  array(
            'headers' => array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('patch')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->patch('test',$params);

        $this->assertEquals(true, $result);
    }

    public function test_patch_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->patch('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_delete_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'query' => array(
                'x' => 1
            )
        );

        $mocks['http']->shouldReceive('delete')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->delete('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_delete_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'headers' => array(
                'Authorization' => 'Bearer 123'
            )
        );

        $mocks['http']->shouldReceive('delete')
            ->once()
            ->with('test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->delete('test', $params);

        $this->assertEquals(true, $result);
    }

    public function test_delete_returns_psr_7_response()
    {
        $test = new GuzzleAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/',
            'exceptions' => false
        ));

        $result = $test->delete('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    protected function getMocks(array $config = null)
    {
        $client = \Mockery::mock(Client::class);

        if(!$config) {
            $config = Yaml::parse(file_get_contents(__DIR__ . '/../../src/Cobwebinfo/ShrekApiClient/config.yaml'));
        }

        $adapter = new GuzzleAdapter($client, $config['http_client_opts']);

        return array(
            'http' => $client,
            'adapter' => $adapter
        );
    }
}