<?php

define('WITHOUT_GROUP', 1);
define('IS_ACTION', '');

class item_import
{

    private $vendors = array();
    private $params = array();
    private $categories = array();
    private $xml;
    private $currency_id;
    private $goods_group_id;
    private $GoodsGroup;
    private $Attributs;
    private $Catalogue;
    private $Brands;
    private $Item;
    private $AnotherPages;
    private $DiscountModel;
    //  private $offer_path = '/yml_catalog/shop/offers/offer[@id=14481]';
    private $related_list_path = '/yml_catalog/shop/offers/offer[relatedList]';
    private $offer_path = '/yml_catalog/shop/offers/offer';

    public function __construct()
    {
        Zend_Loader::loadClass('models_GoodsGroup');
        Zend_Loader::loadClass('models_Attributs');
        Zend_Loader::loadClass('models_Catalogue');
        Zend_Loader::loadClass('models_Brands');
        Zend_Loader::loadClass('models_Item');
        Zend_Loader::loadClass('models_AnotherPages');
        Zend_Loader::loadClass('models_Discounts');
        Zend_Loader::loadClass('Zend_Exception');

        $this->GoodsGroup = new models_GoodsGroup();
        $this->Brands = new models_Brands();
        $this->Attributs = new models_Attributs();
        $this->Catalogue = new models_Catalogue();
        $this->Item = new models_Item();
        $this->AnotherPages = new models_AnotherPages();
        $this->DiscountModel = new models_Discounts();
    }

    public function loadXMLFile($file_name)
    {
        try {
            if (!is_file($file_name))
                throw new Exception('File ' . $file_name . ' is not exist');

            $content = file_get_contents($file_name);

            if (empty($content)) {
                throw new Exception('XML data is empty');
            }

            $this->xml = new DOMDocument;
            $this->xml->loadXML($content);
        } catch (Exception $exc) {
            echo $exc;
            echo $exc->getMessage();
        }
    }

    public function run()
    {
        $this->setCurrency();
        $this->setVendors();
        $this->setCategories(0);
        $this->setParams();
        $this->setOffers();

        $this->setRelatedList();

        $this->sequencesUpdate();

        $this->createRealCatName();

        $sefu = new CreateSEFU();
        $sefu->applySEFU();

        $this->Catalogue->trancuteCatItem();

        $this->checkCatalogueCount(array(0));
    }

    private function setCurrency()
    {
        $path = '/yml_catalog/shop/currency';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            if ($entries->length == 0)
                throw new Exception('Node currency is not exist');

            $code = '';
            foreach ($entries as $key => $value) {
                $code = $value->getAttribute('code');
            }

            if (empty($code))
                throw new Exception('Node currency is empty');

            $this->currency_id = $this->Catalogue->getCurrencyId($code);
        } catch (Exception $exc) {
            echo $exc;
        }

        unset($xpath);
        unset($entries);
    }

    private function setVendors()
    {
        $path = '/yml_catalog/shop/vendors/vendor';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            //      if($entries->length==0) throw new ExceptionEmptyBuffer('Node vendors is not exist');

            if ($entries->length > 0) {
                foreach ($entries as $key => $value) {
                    $code = $value->getAttribute('id');
                    $vendor = trim($value->nodeValue);

                    $vendor = htmlentities($vendor, ENT_QUOTES, 'utf-8');

                    $vendor_id = $this->Brands->getBrandByCode($code);
                    if (empty($vendor_id)) {
                        $vendor_id = $this->Brands->getBrandByName($vendor);
                    }

                    $alt_name = $this->translit($vendor);
                    $alt_name = mb_strtolower($alt_name, 'utf-8');
                    $alt_name = preg_replace("/\s+/s", '-', $alt_name);

                    if (empty($vendor_id)) {
                        $_data['ID_FROM_VBD'] = $code;
                        $_data['NAME'] = $vendor;
                        $_data['ALT_NAME'] = $alt_name;
                        $_data['STATUS'] = 1;

                        $vendor_id = $this->Brands->insertBrand($_data);
                    } else {
                        $_data['ID_FROM_VBD'] = $code;
                        $_data['ALT_NAME'] = $alt_name;
                        $this->Brands->updateBrand($_data, $vendor_id);
                    }

                    unset($_data);

                    $this->vendors[$code] = $vendor_id;
                }
            }
        } catch (ExceptionEmptyPath $exc) {
            echo $exc;
        }

        unset($xpath);
        unset($entries);
    }

    private function setCategories($inParentId)
    {
        $path = '/yml_catalog/shop/categories/category[@parentId=' . $inParentId . ']';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            //      if($entries->length==0) throw new ExceptionEmptyBuffer('Node categories is not exist');

            if ($entries->length > 0) {
                foreach ($entries as $key => $value) {
                    $code = $value->getAttribute('id');
                    $parentId = $value->getAttribute('parentId');
                    $category = $value->nodeValue;

                    //        $item_count_path = '/yml_catalog/shop/offers/offer[categoryId='.$code.']';
                    //        $item_count = $xpath->query($item_count_path);

                    $category = htmlentities($category, ENT_QUOTES, 'UTF-8');

                    $category_id = $this->Catalogue->getCatalogueByCode($code);
                    if (empty($category_id)) {
                        $category_id = $this->Catalogue->getCatalogueByName($category);
                    }

                    $parent_id = isset($this->categories[$inParentId]) ? $this->categories[$inParentId] : 0;

                    if (empty($category_id)) {
                        $_data['ID_FROM_VBD'] = $code;
                        $_data['PARENT_ID'] = $parent_id;
                        $_data['NAME'] = $category;
                        $_data['CATNAME'] = $this->getCatName($category);
                        $_data['COUNT_'] = 0;
                        $_data['ORDERING'] = $this->Catalogue->getOrdering($parent_id);
                        $_data['REALSTATUS'] = 1;
                        $_data['STATUS'] = 1;

                        $category_id = $this->Catalogue->insertCatalogue($_data);
                        unset($_data);
                    } else {
                        $ordering = $this->Catalogue->getCatalogOrdering($category_id);

                        $_data['PARENT_ID'] = $parent_id;
                        $_data['ORDERING'] = !empty($ordering) ? $ordering : $this->Catalogue->getOrdering($parent_id);
                        $_data['ID_FROM_VBD'] = $code;
                        $_data['CATNAME'] = $this->getCatName($category);

                        $this->Catalogue->updateCatalogue($_data, $category_id);
                        unset($_data);
                    }

                    $this->categories[$code] = $category_id;

                    $path_child = '/yml_catalog/shop/categories/category[@parentId=' . $code . ']';
                    $path_child_count = $xpath->query($path_child);
                    if ($path_child_count->length > 0)
                        $this->setCategories($code);
                }
            }
        } catch (ExceptionEmptyPath $exc) {
            echo $exc;
        }
        unset($xpath);
        unset($entries);
    }

    private function getCatName($name)
    {
        $rules = $this->AnotherPages->getTranslitRules();

        $name = trim(mb_strtolower($name, 'utf-8'));
        $name = preg_replace("/\s+/s", "-", $name);

        return strtr($name, $rules);
    }

    private function createRealCatName()
    {
        if (!empty($this->categories)) {
            foreach ($this->categories as $category_id) {
                $_data['REALCATNAME'] = $this->getRealCatName($category_id);
                $this->Catalogue->updateCatalogue($_data, $category_id);
            }
        }
    }

    private function getRealCatName($id)
    {
        list ($PATH, $PARENTID, $NAME) = array('', '', '');
        $i = 0;
        while ($result = $this->AnotherPages->getCatName($id)) {
            $PARENTID = $result['PARENT_ID'];
            $NAME = $result['CATNAME'];

            $i++;
            if ($i == 1 && $NAME == '')
                break;
            $id = $PARENTID;
            if ($NAME) {
                $PATH = "/$NAME$PATH";
            }
        };

        if ('/' != substr($PATH, -1)) {
            $PATH = $PATH . "/";
        }
        $PATH = preg_replace("/(\/){1,}/", "/", $PATH);

        return $PATH;
    }

    private function setParams()
    {
        $path = '/yml_catalog/shop/params/param';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            //      if($entries->length==0) throw new ExceptionEmptyBuffer('Node params is not exist');

            if ($entries->length > 0) {
                foreach ($entries as $key => $value) {
                    $code = $value->getAttribute('id');
                    $type = $value->getAttribute('type');
                    $attribute = trim($value->getElementsByTagName('name')->Item(0)->nodeValue);

                    $attribute = htmlentities($attribute, ENT_QUOTES, 'UTF-8');

                    $pattern = '/^(.*)(,(.*))?$/Uis';
                    preg_match($pattern, $attribute, $out);

                    $attribute_name = (isset($out[1]) && !empty($out[1])) ? trim($out[1]) : $attribute;
                    $unit_name = (isset($out[3]) && !empty($out[3])) ? trim($out[3]) : '';

                    $attribute_id = $this->Attributs->getAttributByCode($code);

                    if (empty($attribute_id)) {
                        $attribute_id = $this->Attributs->getAttributByName($attribute_name, $type);
                    }

                    $_data['ID_FROM_VBD'] = $code;
                    $_data['NAME'] = $attribute_name;
                    if (empty($attribute_id)) {
                        $attribut_group_id = $this->getAttributGroupID($code);

                        if (!empty($unit_name)) {
                            $_data['UNIT_ID'] = $this->Attributs->getUniteByName($unit_name);
                        }

                        $_data['UNIT_ID'] = !empty($_data['UNIT_ID']) ? $_data['UNIT_ID'] : 0;

                        $_data['ATTRIBUT_GROUP_ID'] = $attribut_group_id;
                        $_data['TYPE'] = ($type == 10) ? 3 : $type;
                        $_data['ORDERING'] = $this->Attributs->getAttributOrder($attribut_group_id);;
                        $_data['STATUS'] = 1;

                        $attribute_id = $this->Attributs->insertAttribut($_data);
                        unset($_data);
                    } else {
                        $_data['TYPE'] = ($type == 10) ? 3 : $type;
                        $this->Attributs->updateAttribut($_data, $attribute_id);
                        unset($_data);
                    }

                    $this->params[$code]['id'] = $attribute_id;
                    $this->params[$code]['type'] = $type;

                    if ($type == 10 && !empty($attribute_id)) {
                        if ($value->getElementsByTagName('values')->length > 0) {
                            $values = $value->getElementsByTagName('values')->Item(0)->getElementsByTagName('value');
                            foreach ($values as $val) {
                                unset($_data);
                                $val_id = $val->getAttribute('id');

                                $attribut_list_id = $this->Attributs->hasAttributList($val->nodeValue, $attribute_id);
                                if (empty($attribut_list_id)) {
                                    $_data['ATTRIBUT_ID'] = $attribute_id;
                                    $_data['NAME'] = $val->nodeValue;

                                    $attribut_list_id = $this->Attributs->insertAttributList($_data);
                                }

                                $this->params[$code]['values'][$val_id] = $attribut_list_id;
                            }
                        }
                    }

                    unset($attribute_id);
                }
            }
        } catch (ExceptionEmptyPath $exc) {
            echo $exc;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        unset($xpath);
        unset($entries);
    }


    /**
     * Получить ID группы атрибутов
     *
     * @param int $attribut_code
     *
     * @return int $attribut_group_id
     */
    private function getAttributGroupID($attribut_code)
    {
        $attribut_group_id = $category_id = 0;
        $category_name = '';

        $path = '//offer//atribut[@id=' . $attribut_code . ']/../../categoryId';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            if ($entries->length > 0) {
                foreach ($entries as $key => $value) {
                    $category_id = (int)$value->nodeValue;
                    break;
                }

                if (!empty($category_id)) {
                    $category_name = $this->getCategoryName($category_id);
                }

                if (!empty($category_name)) {
                    $attribut_group_id = $this->Attributs->getAttributGroupByName($category_name);

                    if (empty($attribut_group_id)) {
                        $_data['NAME'] = $category_name;
                        $_data['ORDERING'] = $this->Attributs->getAttributGroupOrder();;

                        $attribut_group_id = $this->Attributs->insertAttributGroup($_data);
                    }

                }
            }
        } catch (ExceptionEmptyPath $exc) {
            echo $exc;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        unset($xpath);
        unset($entries);

        return $attribut_group_id;
    }

    /**
     * Получить имя категории по XPath
     *
     * @param int $category_id
     *
     * @return string $category_name
     */
    private function getCategoryName($category_id)
    {
        $category_name = '';

        $path = '//category[@id=' . $category_id . ']';

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($path);
        try {
            if ($entries->length > 0) {
                foreach ($entries as $key => $value) {
                    $category_name = (string)$value->nodeValue;
                    $category_name = trim($category_name);
                    break;
                }
            }
        } catch (ExceptionEmptyPath $exc) {
            echo $exc;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        unset($xpath);
        unset($entries);

        return $category_name;
    }

    private function setOffers()
    {

        $this->goods_group_id[IS_LIDER] = $this->GoodsGroup->getGroupIDIndent(IS_LIDER);
        $this->goods_group_id[IS_RECOMEND] = $this->GoodsGroup->getGroupIDIndent(IS_RECOMEND);

        if (!empty($this->goods_group_id[IS_LIDER]))
            $this->GoodsGroup->deleteOldRecored($this->goods_group_id[IS_LIDER]);
        if (!empty($this->goods_group_id[IS_RECOMEND]))
            $this->GoodsGroup->deleteOldRecored($this->goods_group_id[IS_RECOMEND]);

        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($this->offer_path);
        //     echo "Limit Offer ".__LINE__." - ".memory_get_usage()."\r\n";
        try {
            if ($entries->length == 0) {
                throw new Exception('Node offers is not exist');
            }
            //        if (empty($this->categories) || empty($this->params)) {
            //          $this->Item->deactiveAllItems();
            //        }

            if (empty($this->categories)) {
                $this->Item->deactiveAllItems();
            }

            foreach ($entries as $key => $value) {
                //        $pictures = array();
                $article = $value->getAttribute('article');
                $publish = $value->getAttribute('publish');
                $id = $value->getAttribute('id');
                if (empty($article)) {
                    echo "Article is null for ID = $id \r\n<br />";
                    continue;
                }

                $is_lider = $value->getAttribute('IS_LIDER');
                $is_recomend = $value->getAttribute('IS_RECOMEND');
                $is_action = $value->getAttribute('IS_ACTION');

                if( null == $value->getElementsByTagName('price')->Item(0)){
                    echo "Article price $article is empty. Not allowed! \r\n";
                    continue;
                }
                $price = $value->getElementsByTagName('price')->Item(0)->nodeValue;

                $categoryId = ($value->getElementsByTagName('categoryId')->length > 0) ? $value->getElementsByTagName('categoryId')->Item(0)->nodeValue : 0;
                $vendorId = ($value->getElementsByTagName('vendorId')->length > 0) ? $value->getElementsByTagName('vendorId')->Item(0)->nodeValue : 0;
                $name = $value->getElementsByTagName('name')->Item(0)->nodeValue;

                $last_action = ($value->getElementsByTagName('LAST_ACTION')->length > 0) ? $value->getElementsByTagName('LAST_ACTION')->Item(0)->nodeValue : 0;
                $last_delivery = ($value->getElementsByTagName('LAST_DELIVERY')->length > 0) ? $value->getElementsByTagName('LAST_DELIVERY')->Item(0)->nodeValue : 0;


                //        echo "Limit Offer ".__LINE__." - ".memory_get_usage()."\r\n";

                $name = htmlentities($name, ENT_QUOTES, 'UTF-8');

                $warranty = $value->getElementsByTagName('Warranty')->length ? $value->getElementsByTagName('Warranty')->Item(0)->nodeValue : '';

                $_data['WARRANTY_ID'] = 0;
                if (!empty($warranty)) {
                    $_data['WARRANTY_ID'] = (int)$this->Item->getWarrantyByCode($warranty);
                }


                if ($last_delivery > 0) {
                    $_data['DELIVERY_ID'] = (int)$this->Item->getDeliveryByCode($last_delivery);
                }

                $item_id = $this->Item->getItemByCode($article);

                if (empty($item_id) && !isset($this->categories[$categoryId])) {
                    echo "Item with article {$article} not found \r\n<br />";
                    continue;
                }

                if (empty($item_id)) {
                    $item_id = $this->Item->getItemByName($item_id, $this->categories[$categoryId]);
                }

                if (!empty($this->categories)) {
                    $_data['CATALOGUE_ID'] = (int)$this->categories[$categoryId];
                }

                if (!empty($this->vendors)) {
                    $_data['BRAND_ID'] = (int)$this->vendors[$vendorId];
                }

                $_data['CURRENCY_ID'] = (int)$this->currency_id;
                $_data['ARTICLE'] = $article;
                $_data['NAME'] = $name;
                $_data['CATNAME'] = $this->getCatName($name);
                $_data['PRICE'] = str_replace(',', '.', $price);
                $_data['STATUS'] = $publish ? 1 : 0;
                $_data['IS_ACTION'] = $is_action ? 1 : 0;
                $_data['NEED_RESIZE'] = 1;


                // LAST_ACTION переводим на привязку с таблицей DISCOUNTS
                if (!empty($last_action)) {
                    $_data['DISCOUNT_ID'] = $this->DiscountModel->getDiscountId($last_action);
                }

                if (empty($item_id)) {

                    $_data['DATE_INSERT'] = date("Y-m-d");;
                    $item_id = $this->Item->insertItem($_data);
                    //          echo "--> ".$item_id." == {$_data['ARTICLE']} <br /><br />";
                    //          if(empty($item_id)){
                    //            var_dump($_data);
                    //            exit;
                    //          }
                    unset($_data);

                    $_data['CATALOGUE_ID'] = $this->categories[$categoryId];
                    $_data['ITEM_ID'] = $item_id;

                    $this->Item->insertCatItem($_data);
                    unset($_data);
                } else {
                    $this->Item->updateItemImport($_data, $item_id);
                    unset($_data);
                }

                if ($value->getElementsByTagName('pictures')->length > 0) {
                    $_picture = $value->getElementsByTagName('pictures')->Item(0)->getElementsByTagName('picture');
                    $this->addItemImages($_picture, $item_id);
                }


//                if ($last_action > 0) {
//                    $_goods_group_data['GOODS_GROUP_ID'] = (int)$this->GoodsGroup->getGroupIDIndentXml($last_action);
//                    $_goods_group_data['CATALOGUE_ID'] = isset($this->categories[$categoryId]) ? $this->categories[$categoryId] : $this->Item->getItemCatalog($item_id);
//                    $_goods_group_data['ITEM_ID'] = (int)$item_id;
//                    $_goods_group_data['PARENT_ID'] = (int)$this->Catalogue->getParentId($_goods_group_data['CATALOGUE_ID']);
//                    $_goods_group_data['STATUS'] = 1;
//
//                    //            if ($is_lider == 'true') {
//                    //              $this->insertItemToGoodGroup(IS_LIDER, $_goods_group_data);
//                    //            }
//                    //            if ($is_recomend == 'true') {
//                    //              $this->insertItemToGoodGroup(IS_RECOMEND, $_goods_group_data);
//                    //            }
//
//                    if (!empty($_goods_group_data['GOODS_GROUP_ID'])) {
//                        $this->insertItemToGoodGroup(0, $_goods_group_data);
//                    }
//
//                    unset($_goods_group_data);
//                }


                if ($value->getElementsByTagName('params')->length > 0) {
                    $params = $value->getElementsByTagName('params')->Item(0)->getElementsByTagName('atribut');

                    $this->addItemAttribut($params, $categoryId, $item_id);

                    unset($params);
                }
            }
        } catch (ExceptionEmptyBuffer $exc) {
            echo $exc;
        }
    }

    private function setRelatedList()
    {
        $xpath = new DOMXPath($this->xml);
        $entries = $xpath->query($this->related_list_path);

        try {
            if ($entries->length == 0)
                return '';

            $this->Item->truncateItemItem();
            foreach ($entries as $key => $value) {
                $article = $value->getAttribute('article');

                if ($value->getElementsByTagName('relatedList')->length > 0) {

                    $real_item_id = $this->Item->getItemByCode($article);

                    $related_id = $value->getElementsByTagName('relatedList')->Item(0)->getElementsByTagName('relatedId');

                    foreach ($related_id as $val) {
                        $id = $val->nodeValue;
                        $article = $xpath->query('/yml_catalog/shop/offers/offer[@id=' . $id . ']/@article');
                        if ($article->length > 0) {
                            $item_id = $this->Item->getItemByCode($article->Item(0)->nodeValue);
                            if (!empty($item_id)) {
                                $data['ITEM_ITEM_ID'] = $item_id;
                                $data['CATALOGUE_ID'] = $this->Item->getItemCatalog($item_id);
                                $data['ITEM_ID'] = $real_item_id;
                                $data['STATUS'] = 1;

                                $this->Item->insertItemItem($data);
                            }
                        }
                    }
                }
            }
        } catch (ExceptionEmptyBuffer $exc) {
            echo $exc;
        }
    }

    private function addItemImages($_picture, $item_id)
    {
        try {
            foreach ($_picture as $k => $val) {
                $pictures['name'] = $val->nodeValue;
                $pictures['type'] = $val->getAttribute('type');

                //          echo $item_id . "--" . $pictures['name'] . "\r\n";


                if ($pictures['type'] == 'base') {
                    $_data['BASE_IMAGE'] = $pictures['name'];
                    $this->Item->updateItemImport($_data, $item_id);
                } else {
                    if (!$this->Item->itemHasItemImage($pictures['name'], $item_id)) {
                        $_data['ITEM_ID'] = $item_id;
                        $_data['NAME'] = $pictures['name'];
                        $_data['STATUS'] = 1;

                        $this->Item->insertItemFotos($_data);
                    }
                }

                unset($_data);

                if (!file_exists(IMAGE_UPLOAD_PATH . $pictures['name'])) {
                    throw new Exception("cant find image " . $pictures['name']);
                }
            }

        } catch (Exception $exc) {
            echo $exc->getMessage() . "\r\n<br>";
            //          echo $exc->getTraceAsString();
        }
    }

    private function addItemAttribut($params, $categoryId, $item_id)
    {
        foreach ($params as $val) {
            $atribut_id = $val->getAttribute('id');
            $atribut_value = $val->getElementsByTagName('value')->Item(0)->nodeValue;

            $atribut_value = trim($atribut_value);

            if (!empty($atribut_value)) {
                $_get_attrib = $this->getItemParam($atribut_id);

                if ($_get_attrib === false) {
                    throw new Exception('Param ' . $atribut_id . ' is absent in params');
                }

                $atribut_type = $_get_attrib['type'];
                $atribut_real_id = $_get_attrib['id'];

                if (!$this->Item->hasAttrCatalogLink($this->categories[$categoryId], $atribut_real_id)) {
                    $_insert_data['CATALOGUE_ID'] = $this->categories[$categoryId];
                    $_insert_data['ATTRIBUT_ID'] = $atribut_real_id;

                    $this->Item->insertAttrCatalogLink($_insert_data);

                    unset($_insert_data);
                }

                $_data['ITEM_ID'] = $item_id;
                $_data['ATTRIBUT_ID'] = $atribut_real_id;

                switch ($atribut_type) {
                    case 2:
                        $_data['VALUE'] = htmlentities($atribut_value, ENT_QUOTES, 'UTF-8');

                        if (!$this->Item->hasItemN('ITEM2', $_data)) {
                            $this->Item->insertItemN('ITEM2', $_data);
                        }
                        break;

                    case 10:
                        if (isset($_get_attrib['values'][$atribut_value])) {
                            $_data['VALUE'] = $_get_attrib['values'][$atribut_value];

                            if (!$this->Item->hasItemN('ITEM0', $_data)) {
                                $this->Item->insertItemN('ITEM0', $_data);
                            }
                        }
                        break;
                }

                unset($atribut_id);
                unset($atribut_value);
                unset($atribut_type);
                unset($atribut_real_id);
                unset($_get_attrib);

                unset($_data);
            }
        }
    }

    private function getItemParam($atribut_id)
    {
        if (isset($this->params[$atribut_id])) {
            return $this->params[$atribut_id];
        } else
            return false;
    }

    private function insertItemToGoodGroup($indent, $_goods_group_data)
    {
        //      $_goods_group_data['GOODS_GROUP_ID'] = $this->goods_group_id[$indent];
        //      if (empty($_goods_group_data['GOODS_GROUP_ID']))
        //        throw new ExceptionEmptyBuffer('Goods group is not define - ' . $indent);

        $this->GoodsGroup->insertItemToGoodGroup($_goods_group_data);
    }

    private function sequencesUpdate()
    {
        $max = $this->Catalogue->getMaxId();
        $max++;
        $this->Catalogue->sequencesUpdate('CATALOGUE', $max);

        $max = $this->Item->getMaxId();
        $max++;
        $this->Catalogue->sequencesUpdate('ITEM', $max);

        $max = $this->Brands->getMaxId();
        $max++;
        $this->Catalogue->sequencesUpdate('BRAND', $max);

        $max = $this->Attributs->getMaxId();
        $max++;
        $this->Catalogue->sequencesUpdate('ATTRIBUT', $max);
    }

    private function checkCatalogueCount($id)
    {
        $summ = 0;

        $cats_id = $this->Catalogue->getCatByParent($id[0]);

        foreach ($cats_id as $cid) {
            $summ += $this->checkCatalogueCount(array_merge(array($cid), $id));
        }

        foreach ($id as $tid) {
            if ($tid) {
                $this->Catalogue->rebuildCatItem($tid, $id[0]);

                $summ += $this->Catalogue->getItemsCountByCat($tid);

                $this->Catalogue->updateCatCount($summ, $tid);
            }
        }

        return $summ;
    }

    private function translit($cyr_str)
    {
        $rules = $this->AnotherPages->getTranslitRules();

        return strtr($cyr_str, $rules);
    }

}