<?php namespace Cobwebinfo\ShrekApiClient\Model;

/**
 * Keyword entity.
 *
 * @package Cobwebinfo\ShrekApiClient\Model
 */
class Keyword
{
    /**
     * Select a keyword by id
     *
     * @var int|array
     */
    public $id;

    /**
     * Select a keyword by name
     *
     * @var string|array
     */
    public $name;

    /**
     * Select a keyword by URL.
     *
     * @var string|array
     */
    public $url;

    /**
     * Select a keyword by creation date.
     *
     * @var string|array
     */
    public $created_at;

    /**
     * Select a keyword by update date.
     *
     * @var string|array
     */
    public $updated_at;

    /**
     * @var
     */
    public $workflow_status;
}