<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 29.10.13
 * Time: 21:58
 */

/**
 * Class ContextSearch_ElasticSearch_FormatFilter
 */
class ContextSearch_ElasticSearch_FormatFilter implements ContextSearch_ElasticSearch_FormatInterface
{
    /**
     * Query builder array of Elastic search
     *
     * @var array
     */
    private $filter = array();

    /**
     * Query
     *
     * @var array
     */
    private $query = array();

    /**
     * From
     *
     * @var integer
     */
    private $from;

    /**
     * Size elements
     *
     * @var integer
     */
    private $size;

    /**
     *Current position element
     *
     * @var integer
     */
    private $positionElement = array();

    /**
     * Check position
     *
     * @var array
     */
    private $positionElementChild = array();

    /**
     * Bool operator for filter build
     */
    const BOOL_OR = "or";
    const BOOL_AND = "and";

    /**
     * Add term element for filter build
     *
     * @param string $column
     * @param string $value
     * @param string $bool
     */
    public function addFilterTerm($column, $value, $bool = self::BOOL_AND)
    {
        $this->filter[$bool][$this->checkPosition($bool)] = array("term" => array($column => $value));
    }

    /**
     * Add range
     *
     * @param string $column
     * @param integer $minValue
     * @param integer $maxValue
     * @param string $bool
     */
    public function addFilterRange($column, $minValue, $maxValue, $bool = self::BOOL_AND)
    {
        $this->filter[$bool][$this->checkPosition("range")] = array("range" => array($column => array("gt" => $minValue, "lt" => $maxValue)));
    }

    /**
     * Add query term
     *
     * @param string $column
     * @param string $value
     */
    public function addQueryTerm($column, $value)
    {
        $this->query["term"] = array($column => $value);
    }

    /**
     * Add child bool element
     *
     * @param string $column
     * @param string $value
     * @param string $parentBool
     * @param string $childBool
     * @param string $childBoolTree
     */
    public function addFilterTermChild($column, $value, $parentBool = self::BOOL_AND, $childBool = self::BOOL_OR, $childBoolTree = null)
    {
        if (!is_null($childBoolTree)) {
            $this->filter[$parentBool][$this->checkPosition($childBool)][$childBool][$this->checkPositionChild($childBoolTree)][$childBoolTree][] = array("term" => array($column => $value));

            return;
        }

        $this->filter[$parentBool][$this->checkPosition($childBool)][$childBool][$this->checkPositionChild($childBoolTree)] = array("term" => array($column => $value));
    }

    /**
     * Check position element
     *
     * @param string $keyPosition
     * @return mixed
     */
    private function checkPosition($keyPosition)
    {
        if (!isset($this->positionElement[$keyPosition])) $this->positionElement[$keyPosition] = count($this->positionElement);

        return $this->positionElement[$keyPosition];
    }

    /**
     * Check position element
     *
     * @param string $subKeyPosition
     * @return mixed
     */
    private function checkPositionChild($subKeyPosition)
    {
        if (!isset($this->positionElementChild[$subKeyPosition])) $this->positionElementChild[$subKeyPosition] = count($this->positionElementChild);

        return $this->positionElementChild[$subKeyPosition];
    }

    /**
     * Get query
     *
     * @return mixed
     */
    public function getFormatQuery()
    {
        $resultQuery["filtered"]["query"] = $this->query;
        $resultQuery["filtered"]["filter"] = $this->filter;

        return $resultQuery;
    }

    /**
     * Set from
     *
     * @param $from
     * @return mixed
     */
    public function setFrom($from)
    {
        $this->from = $from;
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