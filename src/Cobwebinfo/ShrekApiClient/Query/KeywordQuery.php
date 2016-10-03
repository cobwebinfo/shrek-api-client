<?php namespace Cobwebinfo\ShrekApiClient\Query;

use Cobwebinfo\ShrekApiClient\Model\Keyword;

/**
 * Class KeywordQuery
 *
 * @package Cobwebinfo\ShrekApiClient\Query
 */
class KeywordQuery extends Query
{
    /**
     * @var array
     */
    protected $relations = [
        'organisations',
        'snippets'
    ];

    /**
     * KeywordQuery constructor.
     *
     * @param array $initValues
     */
    public function __construct(array $initValues)
    {
        $this->model = new Keyword();

        parent::__construct($initValues);
    }
}