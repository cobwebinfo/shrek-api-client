<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Query\KeywordQuery;

class KeywordQueryTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor_sets_valid_properties()
    {
       $init = array(
           'id' => 10,
           'name' => 'Test',
           'has' => ['documents']
       );

        $keywordQuery = new KeywordQuery($init);

        $values = $keywordQuery->toArray();

        $this->assertEquals(10, $values['id']);
        $this->assertEquals('Test', $values['name']);
        $this->assertEquals(['documents'], $values['has']);
    }

    public function test_array_properties_support_string()
    {
        $init = array(
            'has' => 'documents'
        );

        $keywordQuery = new KeywordQuery($init);

        $values = $keywordQuery->toArray();

        $this->assertEquals(['documents'], $values['has']);
    }

    public function test_fields_returned_as_string()
    {
        $init = array(
            'fields' => ['id, name']
        );

        $keywordQuery = new KeywordQuery($init);

        $values = $keywordQuery->toArray();

        $this->assertEquals('id, name', $values['fields']);
    }

    public function test_array_properties_support_multi()
    {
        $init = array(
            'has' => ['documents', 'organisations']
        );

        $keywordQuery = new KeywordQuery($init);

        $values = $keywordQuery->toArray();

        $this->assertEquals(['documents', 'organisations'], $values['has']);
    }

    public function test_invalid_properties_throw_exception()
    {
        $init = array(
            'title' => 'I dont exist',
        );

        $this->expectException(\Exception::class);

        $keywordQuery = new KeywordQuery($init);
    }

    public function test_to_array_only_returns_populated_properties()
    {
        $init = array(
            'id' => 10,
            'name' => 'Test',
            'has' => ['documents']
        );

        $keywordQuery = new KeywordQuery($init);

        $this->assertEquals(3, count($keywordQuery->toArray()));
    }
}