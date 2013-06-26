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
        $this->formatQuery['bool']['must'] = array();
    }

    /**
     * Set Query String
     *
     * @param array $data
     */
    public function setQueryString(array $data)
    {
        foreach ($data as $key => $value) {
            if (!isset($this->formatQuery['bool']['must'])) {
                $this->formatQuery['query_string'] = array("default_field" => $key, "query" => $value);
            }

            $this->formatQuery['bool']['must']['query_string'] = array("default_field" => $key, "query" => $value);
        }
    }

    /**
     * Set Prefix like Filter
     *
     * @param string $prefix
     * @param array $fields
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
    public function setFrom($from = 0)
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
     * Setter for fields
     *
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->formatQuery['fields'] = $fields;
    }

    /**
     * Set match
     *
     * @param string $nameColumn
     * @param string $value
     * @param float $cutoffFrequency
     */
    public function setMatch($nameColumn, $value, $cutoffFrequency = 0.0001)
    {
        $this->formatQuery['match'] = array($nameColumn => array("query" => $value, "cutoff_frequency" => $cutoffFrequency));
    }


    /**
     * Setter Match all
     */
    public function setMatchAll()
    {
        $this->formatQuery['match_all'] = array("match_all" => array());
    }

    /**
     * Setter for Value
     *
     * @param string $value
     */
    public function setValue($value = "")
    {
        $this->formatQuery['value'] = $value;
    }

    /**
     * Getter Bool
     *
     * @return null
     */
    public function getBool()
    {
        return (isset($this->formatQuery['bool'])) ? array("bool" => array()) : "";
    }

    /**
     * Getter must
     *
     * @return null
     */
    public function getMust()
    {
        return (isset($this->formatQuery['must'])) ? array("must" => array()) : "";
    }

    /**
     * Getter Query String
     *
     * @return array
     */
    public function getQueryString()
    {
        return (isset($this->formatQuery['query_string'])) ? $this->formatQuery['query_string'] : "";
    }

    /**
     * Getter for prefix
     *
     * @return array
     */
    public function getPrefix()
    {
        return (isset($this->formatQuery['prefix'])) ? array("filter" => array("prefix" => $this->formatQuery['prefix'])) : "";
    }

    /**
     * Getter for count
     *
     * @return null
     */
    public function getCount()
    {
        return (isset($this->formatQuery['count'])) ? $this->formatQuery['count'] : "";
    }

    /**
     * Getter for from
     *
     * @return null
     */
    public function getFrom()
    {
        return (isset($this->formatQuery['from'])) ? $this->formatQuery['from'] : 0;
    }

    /**
     * Getter size
     *
     * @return null
     */
    public function getSize()
    {
        return (isset($this->formatQuery['size'])) ? $this->formatQuery['size'] : 10;
    }

    /**
     * Match All
     *
     * @return array|string
     */
    public function getMatchAll()
    {
        return (isset($this->formatQuery['match_all'])) ? $this->formatQuery['match_all'] : "";
    }

    /**
     * Getter Fields
     *
     * @return mixed
     */
    public function getFields()
    {
        return $this->formatQuery['fields'];
    }

    /**
     * Getter for value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->formatQuery['value'];
    }

    /**
     * Getter for Format Query
     *
     * @return array
     */
    public function getFormatQuery()
    {
        return $this->formatQuery;
    }

    /**
     * Clear query
     */
    public function clearQuery()
    {
        $this->formatQuery = array();
    }

}