<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 03.07.14
 * Time: 23:48
 */
use Elastica\Query\Builder;

/**
 * Class ElasticaExtention_Builder
 */
class ElasticaExtension_Builder extends Builder
{
    /**
     * Create object
     *
     * @param null $string
     */
    public function __construct($string = null)
    {
        parent::__construct($string);
    }

    /**
     * Field array set array query if has sub document criteria search
     *
     * @param string $key
     * @param array $value
     *
     * @return Builder
     */
    public function fieldMergeArray($key, array $value)
    {
        $query = substr(0, strlen($this->__toString() - 1)) . "'" . $key . '"' . " : " . "[";

        foreach ($value as $subVal) {
            $query .= json_encode($subVal);
        }

        $query .= "]";

        return new self($query);
    }

    /**
     * Open Field array
     *
     * @param string $key
     *
     * @return Builder
     */
    public function openFieldArray($key)
    {
        $query = substr($this->__toString(), 0, strlen($this->__toString()) - 1) . '"' . $key . '"' . " : " . "[ ";

        return new self($query);
    }

    /**
     * Close field array
     *
     * @return Builder
     */
    public function closeFieldArray()
    {
        return new self(substr($this->__toString(), 0, strlen($this->__toString()) - 1) . "] ");
    }

    /**
     * Build aggregation criteria
     *
     * @param array $aggregation
     *
     * @return ElasticaExtension_Builder
     */
    public function mergeAggregation(array $aggregation)
    {
        $queryAggregation = substr($query = $this->__toString(), 0, strlen($query) - 1) . "," . substr($aggr = json_encode($aggregation), 1);

        return new self($queryAggregation);
    }
} 