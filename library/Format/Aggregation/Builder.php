<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 25.06.14
 * Time: 22:18
 */

use Elastica\Query\Builder;

/**
 * Class Format_Aggregation_Builder
 * @package Aggregation
 */
class Format_Aggregation_Builder
{
    /**
     * Query
     *
     * @var Format_Aggregation_Query
     */
    private $query;

    /**
     * Construct for build object
     *
     * @param array $columns
     */
    public function __construct(array $columns)
    {
        $this->query = new Format_Aggregation_Query(new ElasticaExtension_Builder(), $columns);
    }

    /**
     * Build query aggregation
     *
     * @param Format_Aggregation_ObjectValueAggregation $objectValueAggregation
     *
     * @return string
     */
    public function buildQueryAggregation(Format_Aggregation_ObjectValueAggregation $objectValueAggregation)
    {
        $this->query->initQuery()
            ->initCatalogue($objectValueAggregation->getCatalogueID())
            ->initFilter();

        $attributes = $objectValueAggregation->getAttributes();

        if (!empty($attributes)) $this->query->initAttributes($attributes);

        $priceMin = $objectValueAggregation->getPriceMin();
        $priceMax = $objectValueAggregation->getPriceMax();

        if (!empty($priceMin) && !empty($priceMax)) $this->query->initPrice($priceMin, $priceMax);

        $brands = $objectValueAggregation->getBrands();

        if (!empty($brands)) $this->query->initBrands($brands);

        $this->query->closeFilter();
        $this->query->filteredClose();

        $this->query->initAggregation(
            $objectValueAggregation->getAggregation()
        );

        return $this->query->getJsonResultQuery();
    }
} 