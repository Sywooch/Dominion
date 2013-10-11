<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 05.10.13
 * Time: 23:25
 * To change this template use File | Settings | File Templates.
 */

class ContextSearch_ElasticSearch_FormatFilter implements ContextSearch_ElasticSearch_FormatInterface
{
    /**
     * Terms
     *
     * @var array
     */
    private $terms = array();

    /**
     * Setter query
     *
     * @var array
     */
    private $query = array();
    /**
     * Format bool
     *
     * @var array
     */
    private $bool = array();

    /**
     * Size
     *
     * @var integer
     */
    private $size;

    /**
     * From
     *
     * @var integer
     */
    private $from;

    /**
     * Set Term
     *
     * @param string $columnName
     * @param string $value
     */
    public function setTerms($columnName, $value)
    {
        $this->terms[]['term'][$columnName] = $value;
    }

    /**
     * Setter for from
     *
     * @param integer $from
     * @return mixed|void
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * Set parameter must
     *
     * @param string $bool
     */
    public function setBool($bool)
    {
        $this->bool = $bool;
    }

    /**
     * Set range
     *
     * @param string $columnName
     * @param integer $min
     * @param integer $max
     */
    public function setFromTo($columnName, $min, $max)
    {
        $this->query[]['range'][$columnName] = array("from" => $min, "to" => $max, "include_lower" => true, "include_upper" => true);
    }

    /**
     * Build query
     *
     * @return array
     */
    public function getFormatQuery()
    {
        $query['bool'][$this->bool][] = $this->terms;
        $query['bool'][$this->bool][] = $this->query;

        return $query;
    }

    /**
     * get from
     *
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Setter fro size
     *
     * @param $size
     * @return mixed
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Getter size
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }
}