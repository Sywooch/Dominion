<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 18.06.13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 */

class ContextSearch_ElasticSearch_FormatQuery
{
    /**
     * FormatQuery
     *
     * @var array
     */
    private $formatQuery = array();

    /**
     * Setter for Bool
     */
    public function setBool()
    {
        $this->formatQuery['bool'] = array();
    }

    /**
     * Setter for Must
     */
    public function setMust()
    {
        $this->formatQuery['must'] = array();
    }

    /**
     * Set Query String
     *
     * @param array $data
     */
    public function setQueryString(array $data)
    {
        foreach ($data as $key => $value) {
            $this->formatQuery['query_string'] = array("query_string" => array("default_field" => $key, "query" => $value));
        }
    }

    /**
     * Set Prefix like Filter
     *
     * @param string $prefix
     * @param array  $fields
     */
    public function setPrefix($prefix, array $fields)
    {
        $this->formatQuery['prefix'] = array("prefix" => $prefix, "fields" => $fields);
    }

    /**
     * Setter count
     *
     * @param $count
     */
    public function setCount($count)
    {
        $this->formatQuery['count'] = $count;
    }

    /**
     * Set From
     *
     * @param integer $from
     */
    public function setFrom($from)
    {
        $this->formatQuery['from'] = $from;
    }

    /**
     * Set size
     *
     * @param  integer $size
     */
    public function setSize($size)
    {
        $this->formatQuery['size'] = $size;
    }

    /**
     * Setter facetes
     *
     * @param array $fields
     */
    public function setFacets(array $fields)
    {
        $this->formatQuery['facets'] = $fields;
    }

    /**
     * Getter Bool
     *
     * @return null
     */
    public function getBool()
    {
        return (isset($this->formatQuery['bool'])) ? array("bool" => array()) : null;
    }

    /**
     * Getter must
     *
     * @return null
     */
    public function getMust()
    {
        return (isset($this->formatQuery['must'])) ? array("must" => array()) : null;
    }

    /**
     * Getter Query String
     *
     * @return array
     */
    public function getQueryString()
    {
        return (isset($this->formatQuery['query_string'])) ? $this->formatQuery['query_string'] : array();
    }

    /**
     * Getter for prefix
     *
     * @return array
     */
    public function getPrefix()
    {
        return (isset($this->formatQuery['prefix'])) ? array("filter" => array("prefix" => $this->formatQuery['prefix'])) : array();
    }

    /**
     * Getter for count
     *
     * @return null
     */
    public function getCount()
    {
        return (isset($this->formatQuery['count'])) ? $this->formatQuery['count'] : null;
    }

    /**
     * Getter for from
     *
     * @return null
     */
    public function getFrom()
    {
        return (isset($this->formatQuery['from'])) ? array("from" => $this->formatQuery['from']) : null;
    }

    /**
     * Getter size
     *
     * @return null
     */
    public function getSize()
    {
        return (isset($this->formatQuery['size'])) ? array("size" => $this->formatQuery['size']) : null;
    }


}