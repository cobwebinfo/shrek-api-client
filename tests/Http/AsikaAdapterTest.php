<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Factory\AsikaAdapterFactory;
use Cobwebinfo\ShrekApiClient\Factory\GuzzleAdapterFactory;
use Cobwebinfo\ShrekApiClient\Http\AsikaAdapter;
use Cobwebinfo\ShrekApiClient\Query\KeywordQuery;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

class AsikaAdapterTest extends \PHPUnit_Framework_TestCase
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
            ->with('http://shrek-api.cobwebinfo.com/v1/test', [], array())
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
            ->with('http://shrek-api.cobwebinfo.com/v1/test', [], $params)
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
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->get('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_asika_handles_white_space()
    {
        $test = new AsikaAdapterFactory();

        $query = new KeywordQuery([]);

        $query->where('name', 'hello there ');

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->get('posts', ['query' => $query->toArray()]);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_head_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('head')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', array())
            ->andReturn(true);


        $result = $mocks['adapter']->head('test');

        $this->assertEquals(true, $result);
    }

    public function test_head_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('head')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test?x=1', array())
            ->andReturn(true);

        $result = $mocks['adapter']->head('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_head_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('head')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->head('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_head_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->head('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_put_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('put')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', array())
            ->andReturn(true);


        $result = $mocks['adapter']->put('test');

        $this->assertEquals(true, $result);
    }

    public function test_put_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('put')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', http_build_query($params), array())
            ->andReturn(true);

        $result = $mocks['adapter']->put('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_put_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('put')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->put('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_put_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->put('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_post_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('post')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test','', array())
            ->andReturn(true);


        $result = $mocks['adapter']->post('test');

        $this->assertEquals(true, $result);
    }

    public function test_post_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('post')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', http_build_query($params), array())
            ->andReturn(true);

        $result = $mocks['adapter']->post('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_post_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('post')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->post('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_post_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->post('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }

    public function test_patch_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('patch')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', array())
            ->andReturn(true);


        $result = $mocks['adapter']->patch('test');

        $this->assertEquals(true, $result);
    }

    public function test_patch_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('patch')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', http_build_query($params), array())
            ->andReturn(true);

        $result = $mocks['adapter']->patch('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_patch_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('patch')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->patch('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_patch_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->patch('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
    }



    public function test_delete_includes_base_uri()
    {
        $mocks = $this->getMocks();

        $mocks['http']->shouldReceive('delete')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', array())
            ->andReturn(true);


        $result = $mocks['adapter']->delete('test');

        $this->assertEquals(true, $result);
    }

    public function test_delete_adds_query_string()
    {
        $mocks = $this->getMocks();

        $params = array(
            'x' => 1
        );

        $mocks['http']->shouldReceive('delete')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', http_build_query($params), array())
            ->andReturn(true);

        $result = $mocks['adapter']->delete('test', array(
            'query' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_delete_adds_headers()
    {
        $mocks = $this->getMocks();

        $params = array(
            'Authorization' => 'Bearer 123'
        );

        $mocks['http']->shouldReceive('delete')
            ->once()
            ->with('http://shrek-api.cobwebinfo.com/v1/test', '', $params)
            ->andReturn(true);

        $result = $mocks['adapter']->delete('test', array(
            'headers' => $params
        ));

        $this->assertEquals(true, $result);
    }

    public function test_delete_returns_psr_7_response()
    {
        $test = new AsikaAdapterFactory();

        $test = $test->make(array(
            'base_uri' => 'http://jsonplaceholder.typicode.com/'
        ));

        $result = $test->delete('posts/1', array());

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $result);
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