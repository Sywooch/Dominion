<?php

class Helpers_Item extends App_Controller_Helper_HelperAbstract
{

    private $cart;
    private $compare;
    protected $tabs = array('description' => 'Описание'
    , 'characteristics' => 'Характеристики'
    , 'video' => 'Видеообзор'
    , 'items' => 'С этим товаром покупают'
//    , 'comments' => 'Отзывы'
    );

    public function getIndexItems()
    {
        $result = $this->work_model->FrontGoodsItems();

        if (!empty($result)) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            foreach ($result as $view) {
                $this->doItemXmlNode($view, $curr_info);
            }
        }
    }

    public function getCatItems($params)
    {
        $session = new Zend_Session_Namespace('compare');
        $this->compare = $session->compare;

        $orderMap = $params["order_map"];

        $this->domXml->create_element("order", "", DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED);
        foreach ($params["sort"] as $value) {
            $this->domXml->create_element("sort", $value["name"], DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED);
            $this->domXml->set_attribute(array(
                "url" => $value["url"] . $orderMap[$value["default_state"]]["order"] . "/",
                "class" => $orderMap[$value["default_state"]]["class"],
                "active" => $value["active"]
            ));

            $this->domXml->go_to_parent();
        }

        $this->domXml->go_to_parent();

        $result = $this->work_model->getCatItems($params, $this->lang_id);

        if (!empty($result)) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            foreach ($result as $view) {
                $this->doItemXmlNode($view, $curr_info);
            }
        }
    }

    public function getSearch($params)
    {
        $result = $this->work_model->getSearch($params);

        if (!empty($result)) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            foreach ($result as $view) {
                $this->doItemXmlNode($view, $curr_info);
            }
        }
    }

    public function getComparedList($catalogue_id)
    {
        $session = new Zend_Session_Namespace('compare');
        $this->compare = $session->compare;

        if (!empty($session->compare)) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            foreach ($session->compare as $id) {
                $item = $this->work_model->getItemInfo($id, $this->lang_id);

                $this->doItemXmlNode($item, $curr_info, false, 'compare_list');
            }
        }
    }

    public function getItemInfo($id)
    {
        $result = $this->work_model->getItemInfo($id, $this->lang_id);
        $session = new Zend_Session_Namespace('cart');
        $this->cart = $session->item;

        $session = new Zend_Session_Namespace('compare');
        $this->compare = $session->compare;

        if (!empty($result)) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            $this->doItemXmlNode($result, $curr_info, true);
        }
    }

    public function getTabs($section = '')
    {
        $this->domXml->set_tag('//item', true);
        $i = 1;
        foreach ($this->tabs as $key => $view) {
            $sel = 0;
            if ($key == $section) {
                $sel = 1;
            }

            $this->domXml->create_element('tabs', '', 2);
            $this->domXml->create_element('name', $key);
            $this->domXml->create_element('value', $view);
            $this->domXml->create_element('pos', $i++);
            $this->domXml->create_element('sel', $sel);

            $this->domXml->go_to_parent();
        }
    }

    /**
     * Формирование XML карточки товара
     *
     * @param array $item
     * @param array $curr_info
     * @param boolean $all_info
     */
    private function doItemXmlNode($item, $curr_info, $all_info = false, $node_name = 'item')
    {
        list($new_price, $new_price1) = $this->work_model->recountPrice($item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->params['currency'], $curr_info['PRICE']);

        $item['sh_disc_img_small'] = '';
        $item['sh_disc_img_big'] = '';
        $item['has_discount'] = 0;

        if ($this->params['currency'] > 1) {
            $item['iprice'] = round($new_price, 1);
            $item['iprice1'] = round($new_price1, 1);
        } else {
            $item['iprice'] = round($new_price);
            $item['iprice1'] = round($new_price1);
        }

        $params['currency'] = $this->params['currency'];
        $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');
        $ct_helper = $helperLoader->loadHelper('Cart', $params);
        $ct_helper->setModel($this->work_model);
        $item = $ct_helper->recountPrice($item);

        if (isset($this->cart[$item['ITEM_ID']]) && !empty($this->cart[$item['ITEM_ID']])) {
            $in_cart = 1;
            $in_cart_count = $this->cart[$item['ITEM_ID']]['count'];
        } else {
            $in_cart = 0;
            $in_cart_count = 1;
        }

        if (isset($this->compare[$item['CATALOGUE_ID']][$item['ITEM_ID']]) &&
            !empty($this->compare[$item['CATALOGUE_ID']][$item['ITEM_ID']])
        ) {
            $in_compare = 1;
        } else {
            $in_compare = 0;
        }

        $node_attr = array('item_id' => $item['ITEM_ID']
        , 'price' => $item['iprice']
        , 'price1' => $item['iprice1']
        , 'real_price' => $item['PRICE']
        , 'real_price1' => $item['PRICE1']
        , 'in_cart' => $in_cart
        , 'in_compare' => $in_compare
        , 'in_cart_count' => $in_cart_count
        , 'catalogue_id' => $item['CATALOGUE_ID']
        , 'has_discount' => $item['has_discount']
        , 'active' => $item['STATUS']);

        if (!empty($item['WARRANTY_ID'])) {
            $node_attr['warranty_id'] = $item['WARRANTY_ID'];
        }
        if (!empty($item['DELIVERY_ID'])) {
            $node_attr['delivery_id'] = $item['DELIVERY_ID'];
        }
        if (!empty($item['CREDIT_ID'])) {
            $node_attr['credit_id'] = $item['CREDIT_ID'];
        }

        $this->domXml->create_element($node_name, '', 2);
        $this->domXml->set_attribute($node_attr);

        $href = $this->lang . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
        $href_goods_category = $item['CATALOGUE_REALCATNAME'];

        $this->domXml->create_element('name', $item['NAME']);
        $this->domXml->create_element('article', $item['ARTICLE']);
        $this->domXml->create_element('brand_name', $item['BRAND_NAME']);
        $this->domXml->create_element('short_description', nl2br($item['DESCRIPTION']));
        $this->domXml->create_element('sname', $curr_info['SNAME']);
        $this->domXml->create_element('nat_sname', $item['SNAME']);

        $this->domXml->create_element('href', $href);
        $this->domXml->create_element('href_goods_category', $href_goods_category);

        $this->itemImages($item);

        if (!empty($item['sh_disc_img_small']) && strchr($item['sh_disc_img_small'], "#")) {
            $tmp = explode('#', $item['sh_disc_img_small']);
            $this->domXml->create_element('sh_disc_img_small', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                    'w' => $tmp[1],
                    'h' => $tmp[2]
                )
            );
            $this->domXml->go_to_parent();
        }

        if (!empty($item['sh_disc_img_big']) && strchr($item['sh_disc_img_big'], "#")) {
            $tmp = explode('#', $item['sh_disc_img_big']);
            $this->domXml->create_element('sh_disc_img_big', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                    'w' => $tmp[1],
                    'h' => $tmp[2]
                )
            );
            $this->domXml->go_to_parent();
        }

        if ($all_info) {
            $this->domXml->create_element('typename', $item['TYPENAME']);

            if (isset($item['WARRANTY_DESCRIPTION']) && !empty($item['WARRANTY_DESCRIPTION']))
                $this->setXmlNode($item['WARRANTY_DESCRIPTION'], 'warranty_description');

            if (isset($item['DELIVERY_DESCRIPTION']) && !empty($item['DELIVERY_DESCRIPTION']))
                $this->setXmlNode($item['DELIVERY_DESCRIPTION'], 'delivery_description');

            if (isset($item['CREDIT_DESCRIPTION']) && !empty($item['CREDIT_DESCRIPTION']))
                $this->setXmlNode($item['CREDIT_DESCRIPTION'], 'credit_description');

            $long_text = $this->work_model->getItemLognText($item['ITEM_ID']);

            if (!empty($long_text)) {
                $this->setXmlNode($long_text, 'long_text');
            } else
                unset($this->tabs['description']);

            $this->itemItem($item['ITEM_ID'], $curr_info);

            $this->getItemAttributs($item);

            $this->getItemPhotos($item['ITEM_ID']);

            $this->getItemMedia($item['ITEM_ID']);

//            $this->getItemComments($item['ITEM_ID']);
        }

        $this->domXml->go_to_parent();
    }

    /**
     * Формирование XMK фото товара
     *
     * @param array $item
     */
    private function itemImages($item)
    {
        if (!empty($item['IMAGE1']) && strchr($item['IMAGE1'], "#")) {
            $tmp = explode('#', $item['IMAGE1']);
            $this->domXml->create_element('image_small', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                    'w' => $tmp[1],
                    'h' => $tmp[2]
                )
            );
            $this->domXml->go_to_parent();
        }

        if (!empty($item['IMAGE2']) && strchr($item['IMAGE2'], "#")) {
            $tmp = explode('#', $item['IMAGE2']);
            $this->domXml->create_element('image_middle', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                    'w' => $tmp[1],
                    'h' => $tmp[2]
                )
            );
            $this->domXml->go_to_parent();
        }

        if (!empty($item['IMAGE3']) && strchr($item['IMAGE1'], "#")) {
            $tmp = explode('#', $item['IMAGE3']);
            $this->domXml->create_element('image_big', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                    'w' => $tmp[1],
                    'h' => $tmp[2]
                )
            );
            $this->domXml->go_to_parent();
        }

        if (!empty($item['DISCOUNTS_IMAGE']) && strchr($item['DISCOUNTS_IMAGE'], "#")) {
            $tmp = explode('#', $item['DISCOUNTS_IMAGE']);
            $this->domXml->create_element('discount_image', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                'w' => $tmp[1],
                'h' => $tmp[2]
            ));
            $this->domXml->go_to_parent();
        }
    }

    /**
     * Формирование XML дополнительных фото
     *
     * @param int $item_id
     */
    private function getItemPhotos($item_id)
    {
        $item_photos = $this->work_model->getItemPhotos($item_id);
        if (!empty($item_photos)) {
            $this->domXml->set_tag('//item', true);
            foreach ($item_photos as $view) {
                $this->domXml->create_element('item_photo', '', 2);

                $this->domXml->create_element('name', $view['NAME']);

                if ($view['IMAGE1'] != '' && strchr($view['IMAGE1'], "#")) {
                    $tmp = explode('#', $view['IMAGE1']);
                    $this->domXml->create_element('img_small', '', 2);
                    $this->domXml->set_attribute(array('src' => $tmp[0],
                            'w' => $tmp[1],
                            'h' => $tmp[2]
                        )
                    );
                    $this->domXml->go_to_parent();
                }

                if ($view['IMAGE2'] != '' && strchr($view['IMAGE2'], "#")) {
                    $tmp = explode('#', $view['IMAGE2']);
                    $this->domXml->create_element('img_big', '', 2);
                    $this->domXml->set_attribute(array('src' => $tmp[0],
                            'w' => $tmp[1],
                            'h' => $tmp[2]
                        )
                    );
                }

                $this->domXml->go_to_parent();
            }
        }
    }

    /**
     * Формирование XML дополнительных Видео
     *
     * @param int $item_id
     */
    private function getItemMedia($item_id)
    {
        $item_media = $this->work_model->getItemMedia($item_id);
        if (!empty($item_media)) {
            $this->domXml->set_tag('//item', true);
            foreach ($item_media as $view) {
                $this->domXml->create_element('item_media', '', 2);

                $this->domXml->create_element('name', $view['NAME']);

                if (!empty($view['MEDIA_CODE'])) {
                    $this->domXml->create_element('media_code', $view['MEDIA_CODE'], 3, array(), 1);
                }

                if ($view['MEDIA_FILE'] != '' && strchr($view['MEDIA_FILE'], "#")) {
                    $image = $this->splitImageProperties($view['MEDIA_FILE']);
                    $this->domXml->create_element('media_file', '', 2);

                    $this->domXml->set_attribute(array('src' => $image[0]['src'],
                    ));
                    $this->domXml->go_to_parent();
                }

                $this->domXml->go_to_parent();
            }
        } else
            unset($this->tabs['video']);
    }

    /**
     * Формирование XML дополнительных коментариев
     *
     * @param int $item_id
     */
    private function getItemComments($item_id)
    {
        $responses = $this->work_model->getItemResponses($item_id);
        if (!empty($responses)) {
            $this->domXml->set_tag('//item', true);
            foreach ($responses as $view) {
                $this->domXml->create_element('comments', '', 2);
                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('description', $view['DESCRIPTION']);
                $this->domXml->create_element('date', $view['date']);

                $this->domXml->go_to_parent();
            }

            $this->tabs['comments'] .= ' (' . count($responses) . ')';
        }

    }

    /**
     * Формирование XML связанных товаров
     *
     * @param int $goods_category_id
     * @param int $item_id
     * @param array $curr_info
     */
    private function itemItem($item_id, $curr_info)
    {
        $item_item = $this->work_model->getItemItem($item_id);
        if (!empty($item_item)) {
            $this->domXml->set_tag('//item', true);
            foreach ($item_item as $id) {
                $result = $this->work_model->getItemInfo($id, $this->lang_id);

                $this->doItemXmlNode($result, $curr_info, false, 'item_item');
            }
        } else
            unset($this->tabs['items']);
    }

    /**
     * Формирование XML атрибутов товаров
     *
     * @param int $item_id
     */
    private function getItemAttributs($item)
    {
        $itemAttribute = array();
        $itemAttributes = $this->work_model->getAttributes($item['CATALOGUE_ID'], 'ATTR_CATALOG_LINK');
        $itemAttribute = $this->work_model->getItemAttributes($itemAttributes, $item['ITEM_ID'], $item['CATALOGUE_ID']);

        if (!empty($itemAttribute)) {
            $jj = 0;
            $_vag_id = array();
            foreach ($itemAttribute as $k => $attribute) {
                if ($attribute['not_card'] != 1) {
                    if (!in_array($attribute['view_attribut_group_id'], $_vag_id)) {
                        $_vag_id[] = $attribute['view_attribut_group_id'];
                        $this->domXml->set_tag('//item', true);

                        $this->domXml->create_element('view_attribut_group', '', 2);
                        $this->domXml->set_attribute(array('view_attribut_group_id' => $attribute['view_attribut_group_id']
                        ));

                        $this->domXml->create_element('name', $attribute['vag_name']);
                    }

                    $this->domXml->set_tag('//view_attribut_group[@view_attribut_group_id=' . $attribute['view_attribut_group_id'] . ']', true);

                    $this->domXml->create_element('attributes', '', 2);
                    $this->domXml->set_attribute(array('attribut_id' => $attribute['attribut_id']
                    , 'is_rangeable' => $attribute['is_rangeable']
                    , 'not_card' => $attribute['not_card']));

                    if ($attribute['type'] == 5 || $attribute['type'] == 6)
                        $val = $attribute['val'];
                    else
                        $val = $attribute['value'];

                    $this->domXml->create_element('name', $attribute['name']);
                    $this->domXml->create_element('type', $attribute['type']);
                    $this->domXml->create_element('unit_name', $attribute['unit_name']);
                    $this->domXml->create_element('value', $val);

                    $this->domXml->go_to_parent();
                }
            }
        } else
            unset($this->tabs['characteristics']);
    }

    /**
     * Формирование XML бредкрамба товара
     *
     * @param int $id
     */
    public function getDocPath($id)
    {
        $Catalogue = new models_Catalogue();

        $goods_category_id = $this->work_model->getItemCatalog($id);

        $childs = array();
        $childs[count($childs)] = $goods_category_id;
        $parent = $goods_category_id;

        while ($parent > 0) {
            $cat = $Catalogue->getParents($parent, $this->lang_id);
            $parent = $cat['PARENT_ID'];
            if ($parent == 0)
                break;
            $childs[count($childs)] = $cat['PARENT_ID'];
        }


        $this->domXml->create_element('breadcrumbs', '', 2);
        $this->domXml->set_attribute(array('id' => 0,
                'parent_id' => 0
            )
        );
        $href = '/cat/';

        $this->domXml->create_element('name', 'Весь каталог');
        $this->domXml->create_element('url', $href);

        $this->getSubCatalogPath(0, 0);

        $this->domXml->go_to_parent();


        if (!empty($childs)) {
            $childs = array_reverse($childs);
            foreach ($childs as $key => $view) {
                $parent = $Catalogue->getParents($view, $this->lang_id);
                if (!empty($parent)) {
                    $this->domXml->create_element('breadcrumbs', '', 2);
                    $this->domXml->set_attribute(array('id' => $parent['CATALOGUE_ID'],
                        'parent_id' => $parent['PARENT_ID']
                    ));

                    $href = $this->lang . $parent['REALCATNAME'];

                    $this->domXml->create_element('name', trim($parent['NAME']));
                    $this->domXml->create_element('url', $href);

                    if (($key + 1) == count($childs)) {
                        $this->getCatBrands($parent['CATALOGUE_ID']);
                    } else {
                        $this->getSubCatalogPath($parent['CATALOGUE_ID'], $parent['CATALOGUE_ID']);
                    }

                    $this->domXml->go_to_parent();
                }
            }
        }

        $name = $this->work_model->getItemName($id);
        if (!empty($name)) {
            $this->domXml->create_element('breadcrumbs', '', 2);
            $this->domXml->set_attribute(array('id' => $id
            ));

            $this->domXml->create_element('name', $name);
            $this->domXml->create_element('url', '');
            $this->domXml->go_to_parent();
        }
    }

    private function getCatBrands($id)
    {
        $Catalogue = new models_Catalogue();
        $AnotherPages = new models_AnotherPages();

        $result = $Catalogue->getBrands($id);
        if (!empty($result)) {

            $sefURLCatalogue = $Catalogue->getCatRealCat($id);

            foreach ($result as $view) {
                $this->domXml->create_element('breadcrumbs', '', 2);
                $this->domXml->set_attribute(array('id' => $view['BRAND_ID']
                ));


//                $href = '/cat/' . $id . '/brand/' . $view['BRAND_ID'] . '/';

                $href = "{$sefURLCatalogue}br/b{$view['BRAND_ID']}/";

                $_href = $AnotherPages->getSefURLbyOldURL($href);
                if (!empty($_href))
                    $href = $_href;

                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('url', $href);

                $this->domXml->go_to_parent();
            }

            $this->domXml->create_element('breadcrumbs', '', 2);
            $this->domXml->set_attribute(array('id' => 'brands'
            ));


            $href = $Catalogue->getCatRealCat($id);

            $this->domXml->create_element('name', 'Все производители');
            $this->domXml->create_element('url', $sefURLCatalogue);

            $this->domXml->go_to_parent();
        }
    }

    private function getSubCatalogPath($id, $parent_id)
    {
        $Catalogue = new models_Catalogue();

        $result = $Catalogue->getTree($parent_id);
        if (!empty($result)) {
            foreach ($result as $view) {
                if ($id == $view['CATALOGUE_ID'])
                    continue;
                $this->domXml->create_element('breadcrumbs', '', 2);
                $this->domXml->set_attribute(array('id' => $view['CATALOGUE_ID'],
                        'parent_id' => $view['PARENT_ID']
                    )
                );
                $href = $this->lang . $view['REALCATNAME'];

                $this->domXml->create_element('name', trim($view['NAME']));
                $this->domXml->create_element('url', $href);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getItemMeta($id)
    {
        $meta = $this->work_model->getItemMeta($id);
        $SystemSets = new models_SystemSets();

        if (!empty($meta)) {
            $AnotherPages = new models_AnotherPages();

//      $mailurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $mailurl = 'item-' . $meta['ITEM_ID'];


            $item_name = '';
            if (!empty($meta['TYPENAME']))
                $item_name .= ' ' . $meta['TYPENAME'];
            if (!empty($meta['BRAND_NAME']))
                $item_name .= ' ' . $meta['BRAND_NAME'];
            if (!empty($meta['NAME']))
                $item_name .= ' ' . $meta['NAME'];

            $item_name = trim($item_name);

            $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');

            $soc_helper = $helperLoader->loadHelper('Socials');
            $soc_helper->setDomXml($this->domXml);
            $soc_helper->setModel($AnotherPages);
            $soc_helper->getSocials(htmlspecialchars(urldecode($mailurl)), urldecode($item_name));
            $this->domXml = $soc_helper->getDomXml();

            $this->domXml->create_element('docinfo', '', 2);

            $this->domXml->create_element('title', $meta['TITLE']);
            $this->domXml->create_element('keywords', $meta['KEYWORD_META']);
            $this->domXml->create_element('description', $meta['DESC_META']);

            $this->domXml->go_to_parent();
        }
    }

}
