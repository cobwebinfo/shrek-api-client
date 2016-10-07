<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException;
use Cobwebinfo\ShrekApiClient\Exception\InvalidRelationException;
use Cobwebinfo\ShrekApiClient\Model\Keyword;
use Cobwebinfo\ShrekApiClient\Query\Query;

class MockQuery extends Query {

    public function __construct(array $initValues)
    {
        $this->model = new Keyword();

        parent::__construct($initValues);
    }

    protected $relations = [
        'one',
        'two'
    ];
}

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function test_has_is_chainable()
    {
        $instance = $this->getMockInstance();

        $result = $instance->has('one');

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Tests\MockQuery', $result);
    }

    public function test_has_throws_exception_on_bad_relation()
    {
        $instance = $this->getMockInstance();

        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidRelationException');

        $result = $instance->has('test');
    }

    public function test_throws_exception_on_overwrite_relations()
    {
        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException');

        $instance = new MockQuery([
            'relations' => []
        ]);
    }

    public function test_has_without_params_adds_query()
    {
        $instance = $this->getMockInstance();

        $result = $instance->has('one')
            ->toArray();

        $has = $result['has'];

        $this->assertEquals('one', $has['one']);
    }

    public function test_where_has_adds_query()
    {
        $instance = $this->getMockInstance();

        $result = $instance->whereHas('one', 'name', 'Test 123')
            ->toArray();

        $has = $result['has'];

        $this->assertEquals('name', $has['one']['column']);
        $this->assertEquals('Test 123', $has['one']['value']);
        $this->assertEquals('==', $has['one']['operator']);
    }

    public function test_where_has_supports_non_default_operators()
    {
        $instance = $this->getMockInstance();

        $result = $instance->whereHas('one', 'name', 'Test 123', '>=')
            ->toArray();

        $has = $result['has'];

        $this->assertEquals('name', $has['one']['column']);
        $this->assertEquals('Test 123', $has['one']['value']);
        $this->assertEquals('>=', $has['one']['operator']);
    }

    public function test_with_is_chainable()
    {
        $instance = $this->getMockInstance();

        $result = $instance->with('one');

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Tests\MockQuery', $result);
    }

    public function test_with_throws_exception_on_bad_relation()
    {
        $instance = $this->getMockInstance();

        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidRelationException');

        $result = $instance->with('test');
    }

    public function test_with_without_params_adds_query()
    {
        $instance = $this->getMockInstance();

        $result = $instance->with('one')
            ->toArray();

        $with = $result['with'];

        $this->assertEquals('one', $with['one']);
    }

    public function test_with_constrained_adds_query()
    {
        $instance = $this->getMockInstance();

        $result = $instance->withConstrained('one', 'name', 'Test 123')
            ->toArray();

        $with = $result['with'];

        $this->assertEquals('name', $with['one']['column']);
        $this->assertEquals('Test 123', $with['one']['value']);
        $this->assertEquals('==', $with['one']['operator']);
    }

    public function test_with_constrained_supports_non_default_operators()
    {
        $instance = $this->getMockInstance();

        $result = $instance->withConstrained('one', 'name', 'Test 123', '>=')
            ->toArray();

        $with = $result['with'];

        $this->assertEquals('name', $with['one']['column']);
        $this->assertEquals('Test 123', $with['one']['value']);
        $this->assertEquals('>=', $with['one']['operator']);
    }

    public function test_select_is_chainable()
    {
        $instance = $this->getMockInstance();

        $result = $instance->select('one');

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Tests\MockQuery', $result);
    }

    public function test_select()
    {
        $instance = $this->getMockInstance();

        $result = $instance->select(['one', 'two'])
            ->toArray();

        $this->assertEquals('one, two', $result['fields']);
    }


    public function test_select_with_arrays()
    {
        $instance = $this->getMockInstance();

        $result = $instance->select(['one', 'two'])
            ->toArray();

        $this->assertEquals('one, two', $result['fields']);
    }


    public function test_search_is_chainable()
    {
        $instance = $this->getMockInstance();

        $result = $instance->search('test');

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Tests\MockQuery', $result);
    }

    public function test_search()
    {
        $instance = $this->getMockInstance();

        $result = $instance->search('test')
            ->toArray();

        $this->assertEquals('"test"', $result['q']);
    }

    public function test_where_is_chainable()
    {
        $instance = $this->getMockInstance();

        $result = $instance->where('id', 1);

        $this->assertInstanceOf('Cobwebinfo\ShrekApiClient\Tests\MockQuery', $result);
    }

    public function test_where_throws_exception_on_invalid_column()
    {
        $instance = $this->getMockInstance();

        $this->setExpectedException('Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException');

        $result = $instance->where('test', 1);
    }

    public function test_where_default_operator_checks_equality()
    {
        $instance = $this->getMockInstance();

        $result = $instance->where('id', 1)
            ->toArray();

        $this->assertEquals([
            'value' => 1,
            'operator' => '=='
        ], $result['id']);
    }

    public function test_where_operator_can_be_changed()
    {
        $instance = $this->getMockInstance();

        $result = $instance->where('id', 2, '>=')
            ->toArray();

        $this->assertEquals([
            'value' => 2,
            'operator' => '>='
        ], $result['id']);
    }

    protected function getMockInstance()
    {
        return new MockQuery([]);
    }
}