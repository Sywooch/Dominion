<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 25.06.14
 * Time: 22:18
 */

/**
 * Class Query
 * @package Aggregation
 */
class Format_Aggregation_Query
{
    /**
     * Query builder object of elastica
     *
     * @var ElasticaExtension_Builder
     */
    private $queryBuilder;

    /**
     * Columns constant
     *
     * @var array
     */
    private $columns = array();

    /**
     * Format query aggregation;
     *
     * @var array
     */
    private $formatQueryAggregation = array();

    /**
     * Set builder elastic search
     *
     * @param ElasticaExtension_Builder $builder
     * @param array $columns
     */
    public function __construct(ElasticaExtension_Builder $builder, array $columns)
    {
        $this->queryBuilder = $builder;
        $this->columns = $columns;
    }

    /**
     * Init query for build first open json structure
     *
     * @return $this
     */
    public function initQuery()
    {
        $this->queryBuilder->query()
            ->filteredQuery()
            ->query();

        return $this;
    }

    /**
     * Init catalogue criteria search
     *
     * @param integer $catalogueID
     *
     * @return $this
     */
    public function initCatalogue($catalogueID)
    {
        $this->queryBuilder->term()
            ->field($this->columns["CATALOGUE_ID"], $catalogueID)
            ->fieldClose()
            ->termClose();

        return $this;
    }

    /**
     * Init filter
     *
     * @return $this
     */
    public function initFilter()
    {
        $this->queryBuilder->filter()
            ->fieldOpen("and");
        $this->queryBuilder = $this->queryBuilder->openFieldArray("filters");

        return $this;
    }

    /**
     * Init attributes
     *
     * @param \Format_AttributesIterator $attributesIterator
     *
     * @return $this
     */
    public function initAttributes(\Format_AttributesIterator $attributesIterator)
    {
        $this->queryBuilder->open()
            ->fieldOpen("and")
            ->field("filters", array());


        /** @var $value \Format_AttributesIterator */
        foreach ($attributesIterator as $value) {

            $this->queryBuilder->open()
                ->fieldOpen("nested")
                ->field("path", $this->columns["ATTRIBUTES"])
                ->fieldOpen("query");
            if ($value->IsRange()) {
                $this->queryBuilder->range()
                    ->fieldOpen($value->getID())
                    ->field("from", $value->getValueFrom())
                    ->field("to", $value->getValueTo())
                    ->fieldClose()
                    ->rangeClose();

                continue;
            }

            $this->queryBuilder->fieldOpen("terms")
                ->field($value->getID(), $value->getValue())
                ->fieldClose()
                ->fieldClose()
                ->fieldClose()
                ->fieldClose()
                ->close();
        }

        $this->queryBuilder->fieldClose()
            ->close();

        return $this;
    }

    /**
     * Init brands
     *
     * @param array $brands
     *
     * @return $this
     */
    public function initBrands(array $brands)
    {
        $this->queryBuilder->open()
            ->fieldOpen("terms")
            ->field($this->columns["BRAND_ID"], $brands)
            ->fieldClose()
            ->close();

        return $this;
    }

    /**
     * Init price
     *
     * @param integer $from
     * @param integer $to
     *
     * @return $this
     */
    public function initPrice($from, $to)
    {
        $this->queryBuilder->open()
            ->range()
            ->fieldOpen($this->columns["PRICE"])
            ->field("from", $from)
            ->field("to", $to)
            ->fieldClose()
            ->close()
            ->rangeClose()
            ->close();

        return $this;
    }

    /**
     * Close filter
     *
     * @return $this
     */
    public function closeFilter()
    {
        $this->queryBuilder = $this->queryBuilder->closeFieldArray();
        $this->queryBuilder->fieldClose()->filterClose();

        return $this;
    }

    /**
     * Filtered close
     *
     * @return $this
     */
    public function filteredClose()
    {
        $this->queryBuilder->filteredQueryClose()->queryClose();

        return $this;
    }

    /**
     * Init aggregation
     *
     * @param array $aggregation
     *
     * @return $this
     */
    public function initAggregation(array $aggregation)
    {
        $this->queryBuilder = $this->queryBuilder->mergeAggregation($aggregation);
//        $queryJson = $this->queryBuilder->__toString();
//        $this->formatQueryAggregation = $this->queryBuilder->toArray();
//        $this->formatQueryAggregation["aggs"] = $aggregation;

        return $this;
    }

    /**
     * Get json aggregation format query
     *
     * @return string
     */
    public function getJsonResultQuery()
    {
        return $this->queryBuilder->__toString();
    }

    /**
     * Get array result query format
     *
     * @return array
     */
    public function getArrayResultQuery()
    {
        return $this->queryBuilder->toArray();
    }
} 