<?php
class Helpers_Compare extends App_Controller_Helper_HelperAbstract
{

    private $cart;
    private $compare;


    public function getComparedList($catalogue_id)
    {
        $session = new Zend_Session_Namespace('compare');
        $this->compare = $session->compare;

        if (!empty($session->compare[$catalogue_id])) {
            $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

            foreach ($session->compare[$catalogue_id] as $id => $val) {
                $item = $this->work_model->getItemInfo($id, $this->lang_id);
                if (!empty($item)) {
                    $this->doItemXmlNode($item, $curr_info);
                }

            }
        }
    }


    /**
     * Формирование XML карточки товара
     *
     * @param array   $item
     * @param array   $curr_info
     * @param boolean $all_info
     */
    private function doItemXmlNode($item, $curr_info)
    {
        list($new_price, $new_price1) = $this->work_model->recountPrice($item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->params['currency'], $curr_info['PRICE']);

        if ($this->params['currency'] > 1) {
            $item['iprice'] = round($new_price, 1);
            $item['iprice1'] = round($new_price1, 1);
        } else {
            $item['iprice'] = round($new_price);
            $item['iprice1'] = round($new_price1);
        }

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
        , 'active' => $item['STATUS']);

        $this->domXml->create_element('view_compare_list', '', 2);
        $this->domXml->set_attribute($node_attr);

        $href = $this->lang . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
        $href_goods_category = $item['CATALOGUE_REALCATNAME'];

        $this->domXml->create_element('name', $item['NAME']);
        $this->domXml->create_element('brand_name', $item['BRAND_NAME']);
        $this->domXml->create_element('short_description', nl2br($item['DESCRIPTION']));
        $this->domXml->create_element('sname', $curr_info['SNAME']);
        $this->domXml->create_element('nat_sname', $item['SNAME']);

        $this->domXml->create_element('href', $href);
        $this->domXml->create_element('href_goods_category', $href_goods_category);

        $this->itemImages($item);

        $this->domXml->create_element('article', $item['ARTICLE']);
        $this->domXml->create_element('typename', $item['TYPENAME']);

        $this->getItemAttributs($item);

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
            foreach ($itemAttribute as $k => $attribute) {
                if ($attribute['not_card'] != 1) {
                    $this->domXml->create_element('attributes', '', 2);
                    $this->domXml->set_attribute(array('attribut_id' => $attribute['attribut_id']
                    , 'is_rangeable' => $attribute['is_rangeable']
                    , 'not_card' => $attribute['not_card']));

                    $this->domXml->create_element('name', $attribute['name']);
                    $this->domXml->create_element('type', $attribute['type']);
                    $this->domXml->create_element('unit_name', $attribute['unit_name']);
                    $this->domXml->create_element('value', $attribute['value']);
                    $this->domXml->create_element('val', $attribute['val']);

                    $this->domXml->go_to_parent();
                }
            }
        }
    }
}
