<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 16.11.13
 * Time: 19:04
 */

class Helpers_ObjectValue_ObjectValueValidSelection extends App_Controller_Helper_HelperAbstract
{
    /**
     * Data input
     *
     * @var array
     */
    private $data = array();


    /**
     * Set price
     *
     * @param integer $minPrice
     * @param integer $maxPrice
     * @param integer $currency
     * @param Helpers_ItemSelectionPrice $helperItemPrice
     */
    public function setPrice($minPrice, $maxPrice, $currency, Helpers_ItemSelectionPrice $helperItemPrice)
    {
        list($this->data["price_min"], $this->data["price_max"]) = Format_ConvertDataElasticSelection::getFormatRecountPrice
            (
                $minPrice,
                $maxPrice,
                $currency,
                $helperItemPrice
            );
    }

    /**
     * Set catalogue id
     *
     * @param integer $catalogueID
     */
    public function setCatalogueID($catalogueID)
    {
        $this->data["catalogue_id"] = $catalogueID;
    }

    /**
     * Set attributes
     *
     * @param string $brands
     * @param string $attributes
     */
    public function setAttributes($brands, $attributes)
    {
        $this->data["attributes"] = Format_ConvertDataElasticSelection::getArrayAttributes(
            $attributes . $brands
        );
    }

    /**
     * Create object Selection value
     *
     * @param Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
     * @return Helpers_ObjectValue_ObjectValueSelection
     */
    public function getObjectValueSelection(Helpers_ObjectValue_ObjectValueSelection $objectValueSelection)
    {
        $objectValueSelection->setCatalogueID($this->data["catalogue_id"]);
        $objectValueSelection->setDataAttributesDouble($this->data["attributes"][Format_ConvertDataElasticSelection::NAME_ATTRIBUTES_DOUBLE]);

        if (!empty($this->data["attributes"][Format_ConvertDataElasticSelection::NAME_ATRIBUTES_UNIQUE])) {
            $objectValueSelection->setDataAttributesUnique($this->data["attributes"][Format_ConvertDataElasticSelection::NAME_ATRIBUTES_UNIQUE]);
        }

        $objectValueSelection->setDataBrands($this->data["attributes"]["brands"]);
        $objectValueSelection->setDataSlider("ATTRIBUTES.price", $this->data["price_min"], $this->data["price_max"]);

        return $objectValueSelection;
    }
} 