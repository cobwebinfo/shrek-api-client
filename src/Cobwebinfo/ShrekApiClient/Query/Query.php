<?php namespace Cobwebinfo\ShrekApiClient\Query;

use Cobwebinfo\ShrekApiClient\Exception\InvalidParameterException;
use Cobwebinfo\ShrekApiClient\Exception\InvalidRelationException;
use Cobwebinfo\ShrekApiClient\Exception\MissingModelException;
use Cobwebinfo\ShrekApiClient\Support\Arrayable;

/**
 * Class Query
 *
 * @package Cobwebinfo\ShrekApiClient\Query
 */
abstract class Query implements Arrayable
{
    /**
     * List of relations (these can be
     * used in with/has queries).
     *
     * @var array
     */
    protected $relations = array();

    /**
     * Search term.
     *
     * @var string
     */
    protected $q;

    /**
     * Select keywords with defined
     * relationships.
     *
     * @var array[array]
     */
    protected $has = array();

    /**
     * Select relations with keyword/s.
     *
     * @var array[array]
     */
    protected $with = array();

    /**
     * Fields to include.
     *
     * @var array[string]
     */
    protected $fields = array();

    /**
     * @var
     */
    protected $model;

    /**
     * Query constructor.
     *
     * @param array $initValues
     */
    public function __construct(array $initValues = array())
    {
        if($this->model == null) {
            throw new MissingModelException('Error: Model must be set in constructor.');
        } else if(array_key_exists('relations', $initValues)) {
            throw new InvalidParameterException("Bad parameter: unable to overwrite relations.");
        } else {
            $this->initValues($initValues);
        }
    }

    /**
     * Populates the query.
     *
     * @param $values
     */
    protected function initValues($values)
    {
        foreach($values as $key=>$value) {
            if(property_exists($this->model, $key)) {
                $this->model->{$key} = $value;
            } elseif(property_exists($this, $key)) {
                $this->populateProperty($key, $value);
            } else {
                $this->throwInvalidParamException($key);
            }
        }
    }

    /**
     * Returns any class properties which are
     * populated and relevant to the API. The
     * rest are omitted.
     *
     * @return array
     */
    public function toArray()
    {
        $payload = $this->getQuery();

        if(!empty($payload['fields'])) {
            $payload['fields'] = implode(", ", $payload['fields']);
        }

        return $payload;
    }

    /**
     * Returns class properties as array.
     *
     * @return array
     */
    protected function getQuery()
    {
        $properties = $this->getQueryProperties();

        $modelProperties = get_object_vars($this->model);

        $payload = $properties + $modelProperties;

        $this->removeEmptyFromQuery($payload);

        return $payload;
    }

    /**
     * Populates class member.
     *
     * @param $key
     * @param $value
     */
    protected function populateProperty($key, $value)
    {
        if(!is_array($this->{$key})) {
            $this->{$key} = $value;
            return;
        }

        if(is_array($value)) {
            foreach($value as $nested_value) {
                $this->{$key}[] = $nested_value;
            }
        } else {
            $this->{$key}[] = $value;
        }
    }

    /**
     * Removes any empty properties from
     * return value.
     *
     * @param $properties
     */
    protected function removeEmptyFromQuery(&$properties)
    {
        foreach($properties as $key=>$property) {
            $isEmptyArray = is_array($property) && count($property) == 0;
            $isNull  = $property === null;

            if($isEmptyArray || $isNull) {
                unset($properties[$key]);
            }
        }
    }

    /**
     * Returns properties that are pertinent
     * to query.
     *
     * @return array
     */
    protected function getQueryProperties()
    {
        $payload = array(
            'has' => $this->has,
            'with' => $this->with,
            'q' => $this->q,
            'fields' => $this->fields
        );

        return $payload;
    }

    /**
     * Filters data returned by the api based
     * on the relation provided.
     *
     * @param $name
     * @return $this
     */
    public function has($name)
    {
        if(!in_array($name, $this->relations)) {
            throw new InvalidRelationException(ucfirst($name) . ' is not a valid relation.');
        }

        $this->has[$name] = $name;

        return $this;
    }

    /**
     * Filters data returned by the api based
     * on the relation and query provided.
     *
     * @param $name
     * @param $column
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function whereHas($name, $column, $value, $operator = '==')
    {
        if(!in_array($name, $this->relations)) {
            throw new InvalidRelationException(ucfirst($name) . ' is not a valid relation.');
        }

        $this->has[$name] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        );

        return $this;
    }

    /**
     * Determines what relations will be
     * returned from the API.
     *
     * @param $name
     * @return $this
     */
    public function with($name)
    {
        if(!in_array($name, $this->relations)) {
            throw new InvalidRelationException(ucfirst($name) . ' is not a valid relation.');
        }

        $this->with[$name] = $name;

        return $this;
    }

    /**
     * Determines what relations will be
     * returned from the api
     *
     * @param $name
     * @param $column
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function withConstrained($name, $column, $value, $operator = '==')
    {
        if(!in_array($name, $this->relations)) {
            throw new InvalidRelationException(ucfirst($name) . ' is not a valid relation.');
        }

        $this->with[$name] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        );

        return $this;
    }

    /**
     * Determines what columns will be
     * returned from the API.
     *
     * @param string|array $fields
     * @return $this
     */
    public function select($fields)
    {
        if(is_array($fields)) {
            foreach($fields as $field) {
                $this->fields[] = $field;
            }
        } else {
            $this->fields[] = $fields;
        }

        return $this;
    }

    /**
     * Adds search term to API query.
     *
     * @param $term
     * @return $this
     */
    public function search($term)
    {
        $this->q = '"' . $term . '"';

        return $this;
    }

    /**
     * Adds a where clause to $column
     *
     * @param $column
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function where($column, $value, $operator = '==')
    {
        if(!property_exists($this->model, $column)) {
            throw new InvalidParameterException($column . ' is not a valid column.');
        }

        $this->model->{$column} = array(
            'value' => $value,
            'operator' => $operator
        );

        return $this;
    }

    /**
     * @param $key
     */
    protected function throwInvalidParamException($key)
    {
        $message = $key . ' is not a supported property.';

        throw new InvalidParameterException($message);
    }
}