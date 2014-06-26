<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 25.06.14
 * Time: 22:18
 */

namespace Aggregation;

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
     * Aggregation with brands
     *
     * @var array
     */

    /**
     * Aggregation without brands
     *
     * @var array
     */
    private $aggregation = array();

    /**
     * Construct for build object
     *
     * @param array $columns
     * @param array $aggregationWihBrands
     * @param array $aggregation
     */
    public function __construct(array $columns, array $aggregationWihBrands, array $aggregation)
    {
        $this->query = new Format_Aggregation_Query(new Builder(), $columns);
        $this->aggregation = $aggregation;
        $this->aggregationWithBrands = $aggregationWihBrands;
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
            ->closeQuery()
            ->initFilter();

        $attributes = $objectValueAggregation->getAttributes();

        if (!empty($attributes)) $this->query->initAttributes($attributes);

        $priceMin = $objectValueAggregation->getPriceMin();
        $priceMax = $objectValueAggregation->getPriceMax();

        if (!empty($priceMin) && !empty($priceMax)) $this->query->initPrice($priceMin, $priceMax);

        $brands = $objectValueAggregation->getBrands();

        if (!empty($brands)) $this->query->initBrands($brands);

        $this->query->initAggregation(
            !$objectValueAggregation->getSignBrandsCriteria() ?
                $this->aggregationWithBrands :
                $this->aggregation
        )->closeFilter()
            ->filteredClose();

        return $this->query->getJsonResultQuery();
    }
} 