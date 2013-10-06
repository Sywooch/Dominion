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
    public function buildQuery()
    {
        return array("bool" => array($this->bool => array($this->terms, $this->query)));
    }
}