<?php

/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 03.05.14
 * Time: 0:24
 */
require_once "../../../vendor/autoload.php";

/**
 * Class ContextSearch_ElasticSearch_Aggregation
 */
class  ContextSearch_ElasticSearch_FormatAggregation
{
    /**
     * Aggregation parameters
     *
     * @var array
     */
    private $aggregation = array();

    /**
     * Query builder
     *
     * @var ContextSearch_ElasticSearch_QueryBuilder
     */
    private $queryBuilder;

    /**
     * Index for generate array aggregation
     *
     * @var integer
     */
    private $index = 0;

    /**
     * Index attribute
     *
     * @var integer
     */
    private $indexAttribute;

    /**
     * Set aggregation static arguments
     *
     * @param array $aggregation
     */
    public function __construct(array $aggregation)
    {
        $this->aggregation = $aggregation;
        $this->queryBuilder = new \Elastica\Query\Builder();
    }

    /**
     * Init query
     */
    public function initQuery()
    {
        $this->queryBuilder->query()
            ->filteredQuery();

        return $this;
    }

    /**
     * Init filter
     *
     * @return $this
     */
    public function initFilter()
    {
        $this->queryBuilder
            ->filter()
            ->fieldOpen("and");

        return $this;
    }

    /**
     * Set catalogue id
     *
     * @param integer $catalogId
     * @param string $catalogueKey
     *
     * @return $this
     */
    public function setCatalogue($catalogId, $catalogueKey = "CATALOGUE_ID")
    {
        $this->queryBuilder->query()
            ->term()
            ->field($catalogueKey, $catalogId)
            ->termClose()
            ->queryClose();

        return $this;
    }

    /**
     * Init attributes
     *
     * @return $this
     */
    public function initAttributes()
    {
        $this->queryBuilder->fieldOpen($this->index)
            ->fieldOpen("and");

        return $this;
    }


    /**
     * Set attributes values
     *
     * @param array $attributes
     * @param string $nestedField
     * @param string $fieldAttribute
     * @param string $fieldValue
     *
     * @return $this|bool
     */
    public function setAttributes(
        array $attributes,
        $nestedField = "ATTRIBUTES",
        $fieldAttribute = "ATTRIBUTES.ATTRIBUT_ID",
        $fieldValue = "ATTRIBUTES_VALUE")
    {
        if (empty($attributes)) return false;


        foreach ($attributes as $key => $value) {
            $this->queryBuilder->fieldOpen($key)
                ->fieldOpen("nested")
                ->field("path", $nestedField)
                ->query()
                ->filteredQuery()
                ->filter()
                ->fieldOpen("and")
                ->fieldOpen(0)
                ->term()
                ->field($fieldAttribute, $value["ATTRIBUTE_ID"])
                ->termClose()
                ->fieldClose()
                ->fieldOpen(1)
                ->term()
                ->field($fieldValue, $value["VALUE"])
                ->termClose()
                ->fieldClose()
                ->fieldClose()
                ->filterClose()
                ->filteredQueryClose()
                ->queryClose()
                ->fieldClose()
                ->fieldClose();
        }

        return $this;
    }

    public function setAttributesRange(
        array $attributesRange,
        $nestedField = "ATTRIBUTES",
        $fieldAttribute = "ATTRIBUTES.ATTRIBUT_ID",
        $fieldValue = "ATTRIBUTES_VALUE")
    {
        if (empty($attributesRange)) return false;

        foreach ($attributesRange as $key => $value) {
            $this->queryBuilder->fieldOpen($this->indexAttribute)
                ->fieldOpen("nested")
                ->field("path", $nestedField)
                ->query()
                ->filteredQuery()
                ->filter()
                ->fieldOpen("and")
                ->fieldOpen(0)
                ->term()
                ->field($fieldAttribute, $value["ATTRIBUTE_ID"])
                ->termClose()
                ->fieldClose()
                ->fieldOpen(1)

                ->fieldClose()
                ->fieldClose()
                ->filterClose()
                ->filteredQueryClose()
                ->queryClose()
                ->fieldClose()
                ->fieldClose();
        }
    }
} 