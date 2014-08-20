<?php

class CatController extends App_Controller_Frontend_Action
{

    private $catalogue_id = 0;

    public function init()
    {
        parent::init();

        $Catalogue = new models_Catalogue();
        $this->catalogue_id = $this->_getParam('id');

        $res = $Catalogue->getCatInfo($this->catalogue_id);
        if ($this->catalogue_id == 0 || ($this->catalogue_id > 0 && empty($res))) {
            $this->page_404();
        }
    }

    public function indexAction()
    {
        $Catalogue = new models_Catalogue();
        $SectionAlign = new models_SectionAlign();

        $child_count = $Catalogue->getChildCatCount($this->catalogue_id);

//        $this->getCatalogList($this->catalogue_id);

        if (!empty($child_count)) {
            $this->getCatalogList($this->catalogue_id);
        } else {
            $this->getCatalogItemsList();
        }

        $bn_helper = $this->_helper->helperLoader('Banners');
        $bn_helper->setModel($SectionAlign);
        $bn_helper->setDomXml($this->domXml);
        $bn_helper->getCatalogueBanner($this->catalogue_id);
        $this->domXml = $bn_helper->getDomXml();
    }

    /**
     * Создания узла для пейджинга
     *
     * @param mixed $count
     * @param mixed $page
     * @param mixed $pcount
     */
    public function makeSectionInfo($count, $page, $pcount)
    {
        $this->domXml->set_tag('//page/data', true);
        $this->domXml->create_element('section', '', 2);

        $this->domXml->set_attribute(array('count' => $count
        , 'page' => $page
        , 'pcount' => $pcount
        ));

        $this->domXml->go_to_parent();
    }

    private function getCatalogListNew($catalogParentId){
//        $this->template = 'cat_view.xsl';
        $brand_id = $this->_getParam('brand_id', 0);

        $Catalogue = new models_Catalogue();
        $o_data['id'] = $this->catalogue_id;
        $o_data['currency'] = $this->currency;
        $o_data['brand'] = $brand_id;
        $o_data['cat_real_url'] = $cat_real_url;

        $this->openData($o_data);
    }

    /**
     * Вывод списка подкатегорий текущего каталога
     *
     */
    private function getCatalogList()
    {
        $this->template = 'cat_view.xsl';

        $Catalogue = new models_Catalogue();
//      $Item = new models_Item();

        $brand_id = $this->_getParam('brand_id', 0);

        if (empty($brand_id)) {
            $cat_real_url = $Catalogue->getCatRealCat($this->catalogue_id);
        } else {
            $href = '/cat/' . $this->catalogue_id . '/brand/' . $brand_id . '/';
            $cat_real_url = $AnotherPages->getSefURLbyOldURL($href);
            if (!$is_sel_item)
                array_push($attr_brand_id, $brand_id);
        }

        $o_data['id'] = $this->catalogue_id;
        $o_data['currency'] = $this->currency;
        $o_data['brand'] = $brand_id;
        $o_data['cat_real_url'] = $cat_real_url;

        $this->openData($o_data);



        /* @var $cat_helper Helpers_Catalogue */
        $cat_helper = $this->_helper->helperLoader('Catalogue');
        $cat_helper->setModel($Catalogue);
        $cat_helper->setDomXml($this->domXml);

        $cat_helper->generateCatalogueMenu(0);

        $cat_helper->getCatInfo($this->catalogue_id);
        $cat_helper->getCatalogPath($this->catalogue_id);
        $cat_helper->getCatSubTree($this->catalogue_id);
        $this->domXml = $cat_helper->getDomXml();
    }

    /**
     * Вывод товаров текущего каталога
     *
     */
    private function getCatalogItemsList()
    {
        $Catalogue = new models_Catalogue();
        $Item = new models_Item();
        $AnotherPages = new models_AnotherPages();
        $Attributs = new models_Attributs();

        $per_page = $this->getSettingValue('item_in_cat') ? $this->getSettingValue('item_in_cat') : 15;

        $attr_brand_id = array();
        $active_brands = array();
        $active_attrib = array();
        $active_items = array();
        $attr = array();
        $is_sel_item = false;

        $brand_id = $this->_getParam('brand_id', 0);
        $br = $this->_getParam('br', '');
        $at = $this->_getParam('at', '');
        $ar = $this->_getParam('ar', '');
        $pmin = $this->_getParam('pmin', 0);
        $pmax = $this->_getParam('pmax', 0);
        $st_b = $this->_getParam('stb', 0);
        $sattr = $this->_getParam("sattr", 0);

        if (!empty($br)) {
            preg_match_all('/b(\d+)/', $br, $out);
            if (!empty($out[1])) {
                $is_sel_item = true;
                $attr_brand_id = $out[1];
            }
        }

        if (!empty($at)) {
            $_catalogue_id = $Catalogue->getChildren($this->catalogue_id);
            $_catalogue_id[count($_catalogue_id)] = $this->catalogue_id;
            $_items = $Item->getCatalogItemsID($_catalogue_id);

            $params['at'] = $at;
            $params['items'] = $_items;

            $attr = $Attributs->getAllAttrForSelection($params);

            unset($params);
            unset($_catalogue_id);

            $is_sel_item = true;
        }

        if (empty($br) && !empty($brand_id)) {
            $br = 'b' . $brand_id;
        }

        if (!empty($pmin) || !empty($pmax)) {
            $is_sel_item = true;
        }

        if (empty($brand_id)) {
            $cat_real_url = $Catalogue->getCatRealCat($this->catalogue_id);
        } else {
//        $href = '/cat/'.$this->catalogue_id.'/brand/'.$brand_id.'/';
//        $cat_real_url = $AnotherPages->getSefURLbyOldURL($href);

            $cat_real_url = $Catalogue->getCatRealCat($this->catalogue_id);
            if (!$is_sel_item)
                array_push($attr_brand_id, $brand_id);
        }

        $isp_params['currency_id'] = 2;
        $isp_params['real_currency_id'] = $this->currency;

        $isp_price['min_price'] = $pmin;
        $isp_price['max_price'] = $pmax;

        // Перерасчет цен в валюту товаров
        $isp_helper = $this->_helper->helperLoader('ItemSelectionPrice');
        list($is_params['pmin'], $is_params['pmax']) = $isp_helper->recountPrice($isp_price, $isp_params);

        $is_params['catalogue_id'] = $this->catalogue_id;
        $is_params['brands'] = $attr_brand_id;
        $is_params['nat_pmin'] = $pmin;
        $is_params['nat_pmax'] = $pmax;
        $is_params['currency_id'] = $this->currency;

        if ($is_sel_item) {
            $params = $this->getRequest()->getQuery();

            $parameters = Zend_Registry::get("config")->toArray();
            $readerIni = new Zend_Config_Json(__DIR__ . "/../configs/aggregation.json", "aggregation");
            $aggregation = $readerIni->toArray();

            /** @var $objectValueSelection Helpers_ObjectValue_ObjectValueSelection */
            $objectValueSelection = $this->_helper->helperLoader(
                "ObjectValue_ObjectValueSelection"
            );

            $objectValueSelection->setColumns($parameters["columns"]);
            $objectValueSelection->setAggregationWithBrands($aggregation["with_brands"]);
            $objectValueSelection->setAggregationWithoutBrands($aggregation["without_brands"]);
            $objectValueSelection->setCatalogueID((int)$this->catalogue_id);
            $objectValueSelection->setPriceMin((int)$isp_price["min_price"]);
            $objectValueSelection->setPriceMax((int)$isp_price["max_price"]);
            $objectValueSelection->setCheckBrands(false);

            $formatDataElastic = new Format_FormatDataElastic();

            if (!empty($at) || !empty($ar)) {
                $formatDataElastic->parseRangeAttributes($ar);
                $formatDataElastic->parseAttributesChecked($at);
            }

            $objectValueSelection->setAttributes((array)$formatDataElastic->getAttributesFormatAggregation());

            if (!empty($br)) $objectValueSelection->setBrands((array)$attr_brand_id);

            /** @var $selectionElasticSearch Helpers_SelectionElasticSearch */
            $selectionElasticSearch = $this->_helper->helperLoader(
                "SelectionElasticSearch",
                $objectValueSelection
            );

            $selectionElasticSearch->connect($parameters['search_engine'], "selection");
            $selectionElasticSearch->selection($objectValueSelection);

            $active_items = $selectionElasticSearch->getAggregationResultItems();
            $active_brands = (!$st_b)
                ? $selectionElasticSearch->getAggregationResultBrands()
                : $formatDataElastic->getBrandsFormat($Item->getAllModels(array($this->catalogue_id)));

            if (!empty($sattr)) {
                $formatAttributes = Format_ConvertDataElasticSelection::convertCriteriaQuerySelection($formatDataElastic->getAttributesFormatAggregation(), $sattr);

                $objectValueSelection->setAttributes($formatAttributes);
                $selectionElasticSearch->selection($objectValueSelection);
            }

            $active_attrib = $selectionElasticSearch->getAggregationResultAttributes();
        }

        $isp_params['currency_id'] = $this->currency;
        $isp_params['real_currency_id'] = 2;
        $isp_params['catalogue_id'] = $this->catalogue_id;

        // Узнаем min max цену по каталогу
        $isp_helper = $this->_helper->helperLoader('ItemSelectionPrice');
        $isp_helper->setDomXml($this->domXml);
        $min_max_price = $isp_helper->getPrices($isp_params);

        $isp_params = array_merge($isp_params, $min_max_price);
        $isp_helper->getPricesLine($isp_params);

        $isp_params['brands'] = $attr_brand_id;
        $isp_params['items_id'] = $active_items;
        // Узнаем min max цену по каталогу с учетом подбора
        $current_min_max_price = $isp_helper->getPrices($isp_params);
        $this->domXml = $isp_helper->getDomXml();

        $request = $this->getRequest();
        $attr_gr_id = $request->getCookie('attr_gr_id', 0);

        $o_data['id'] = $this->catalogue_id;
        $o_data['currency'] = $this->currency;
        $o_data['brand'] = $brand_id;
        $o_data['cat_real_url'] = $cat_real_url;
        $o_data['is_sel_item'] = $is_sel_item ? 1 : 0;

        $o_data['min_price'] = !empty($min_max_price['min_price']) ? $min_max_price['min_price'] : 0;
        $o_data['max_price'] = !empty($min_max_price['max_price']) ? $min_max_price['max_price'] : 0;
        $o_data['current_min_price'] = $current_min_max_price['min_price'];
        $o_data['current_max_price'] = $current_min_max_price['max_price'];
        $o_data['show_price_min'] = $pmin;
        $o_data['show_price_max'] = $pmax;
        $o_data['br_page'] = $br;
        $o_data['at_page'] = $at;
        $o_data['ar_page'] = $ar;
        $o_data['cname'] = $Item->getCurrencyName($this->currency);
        $o_data['attr_gr_id'] = $attr_gr_id;

        $this->openData($o_data);

        if (!empty($ar)) {
            $this->setArToDOM($ar, $at);
        }

        $params['Item'] = $Item;

        /** @var $cat_helper Helpers_Catalogue */
        $cat_helper = $this->_helper->helperLoader('Catalogue', $params);
        $cat_helper->setModel($Catalogue);
        $cat_helper->setDomXml($this->domXml);
        $cat_helper->getCatInfo($this->catalogue_id);
        $cat_helper->getCatalogPath($this->catalogue_id);
        $cat_helper->getCompareItems($this->catalogue_id);
        $cat_helper->getAttrBrands($this->catalogue_id, $attr_brand_id, $active_brands);
        $cat_helper->generateCatalogueMenu(0);
        $this->domXml = $cat_helper->getDomXml();

        $xml = $this->domXml->getXML();

        $item_params['brand_id'] = $attr_brand_id;
        $item_params['catalogue_id'] = $this->catalogue_id;
        $item_params['items_id'] = $active_items;

        $child_count = $Catalogue->getChildItemCount($item_params);
        if (!empty($child_count)) {
            $page = $this->_getParam('page', 1);

            $at = '';

            $count = $child_count;
            $per_page = $this->getSettingValue('item_in_cat') ? $this->getSettingValue('item_in_cat') : 15;

            $startSelect = ($page - 1) * $per_page;
            $startSelect = $startSelect > $count ? 0 : $startSelect;
            $startSelect = $startSelect < 0 ? 0 : $startSelect;

            $pcount = ceil($count / $per_page);

            $this->makeSectionInfo($count, $page, $pcount);

            $item_params['currency'] = 1;
            $item_params['start'] = $startSelect;
            $item_params['per_page'] = $per_page;

            $it_helper = $this->_helper->helperLoader('Item', $item_params);
            $it_helper->setLang($this->lang, $this->lang_id);
            $it_helper->setModel($Item);
            $it_helper->setDomXml($this->domXml);
            $it_helper->getCatItems($item_params);
            $this->domXml = $it_helper->getDomXml();

            $attributs_params['Item'] = $Item;
            $attributs_params['Catalogue'] = $Catalogue;

            /** @var $at_helper Helpers_Attributs */
            $at_helper = $this->_helper->helperLoader('Attributs', $attributs_params);
            $at_helper->setLang($this->lang, $this->lang_id);
            $at_helper->setModel($Attributs);
            $at_helper->setDomXml($this->domXml);
            $at_helper->getMinMaxPrice($this->catalogue_id);
            $at_helper->getAttributs($this->catalogue_id, $attr, $active_attrib);
            $this->domXml = $at_helper->getDomXml();
            $xml = $at_helper->getDomXml()->getXML();
        }
    }

    private function setArToDOM($ar, $at)
    {
        $attr_array = explode('a', $ar);
        foreach ($attr_array as $attr_val) {
            if (preg_match('/(\w+)v(\w*)-(\w*)/', $attr_val, $m)) {
                $this->domXml->create_element('attr_range_mm', '', 2);
                $this->domXml->set_attribute(array('id' => $m[1]
                , 'min' => $m[2]
                , 'max' => $m[3]
                ));

                $this->domXml->go_to_parent();
            }
        }
        unset($attr_array);

        $attr_array = explode('a', $at);
        foreach ($attr_array as $attr_val) {
            if (preg_match('/(\w+)v(\w*)-(\w*)/', $attr_val, $m)) {
                $_url = 'a' . $m[1] . 'v' . $m[2] . '-' . $m[3];
                $this->domXml->create_element('attr_range_view_url', '', 2);
                $this->domXml->set_attribute(array('id' => $m[1],
                    'url' => $_url
                ));

                $this->domXml->go_to_parent();
            }
        }
    }

}