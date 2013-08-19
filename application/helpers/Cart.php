<?php
class Helpers_Cart extends App_Controller_Helper_HelperAbstract
{

    public function addCart($item_id, $count)
    {
        $session = new Zend_Session_Namespace('cart');
        if (isset($session->item[$item_id])) {
            $session->item[$item_id]['count']++;
        } else {
            $session->item[$item_id]['count'] = $count;
        }
    }

    public function getBasket()
    {
        $count = 0;
        $sum = 0;
        $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);

        $session = new Zend_Session_Namespace('cart');
        if (!empty($session->item)) {
            foreach ($session->item as $item_id => $view) {
                $price = $this->takeItemPrice($item_id, $curr_info);

                $count += $view['count'];
                $sum += $price * $view['count'];
            }
        }

        return array($count, $sum);
    }

    private function takeItemPrice($id, $curr_info)
    {
        $item = $this->work_model->getItemInfo($id);

        list($new_price, $new_price1) = $this->work_model->recountPrice($item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->params['currency'], $curr_info['PRICE']);

        if ($this->params['currency'] > 1) {
            $item['iprice'] = round($new_price, 1);
            $item['iprice1'] = round($new_price1, 1);
        } else {
            $item['iprice'] = round($new_price);
            $item['iprice1'] = round($new_price1);
        }

        $item = $this->recountPrice($item);

        return ($item['iprice1'] > 0) ? $item['iprice1'] : $item['iprice'];
    }

    public function recountPrice($item)
    {
        $user_data = Zend_Auth::getInstance()->getIdentity();
        if (!empty($user_data) && $item['IS_ACTION'] == 1) {
            $params['price'] = $item['iprice'];
            $params['user_id'] = $user_data['user_id'];

            $params['real_currency_id'] = $item['CURRENCY_ID'];
            $params['currency_id'] = $this->params['currency'];

            $prObj = new models_PriceRecount();
            $_price_result = $prObj->priceRecount($params);
            if (!empty($_price_result['new_price'])) {
                $item['has_discount'] = 1;
                $item['iprice1'] = $_price_result['new_price'];
                $item['iprice'] = $params['price'];

                $item['PRICE1'] = $_price_result['real_price'];

                $item['sh_disc_img_small'] = $_price_result['sh_disc_img_small'];
                $item['sh_disc_img_big'] = $_price_result['sh_disc_img_big'];
            }
        }

        return $item;
    }

    public function getCart()
    {
        $session = new Zend_Session_Namespace('cart', true);

        $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);
        $itogo_summa = 0;
        if (!empty($session->item)) {

            $total_summ = 0;
            foreach ($session->item as $item_id => $view) {
                $price = $this->getCartItem($item_id, $view, $curr_info);
                $total_summ += $price * $view['count'];
            }

            $itogo_summa += $total_summ;

            $this->domXml->create_element('total_summ', $total_summ);
            $this->domXml->create_element('sname', $curr_info['SNAME']);
            $this->domXml->go_to_parent();
        }


        $this->domXml->create_element('itogo_summa', $itogo_summa, 2);
        $this->domXml->go_to_parent();
        $this->domXml->create_element('sname', $curr_info['SNAME'], 2);
        $this->domXml->go_to_parent();
    }

    public function getPayments()
    {
        $payment = $this->work_model->getPayments();
        if (!empty($payment)) {
            foreach ($payment as $view) {
                $this->domXml->create_element('payment', '', 2);
                $this->domXml->set_attribute(array('id' => $view['PAYMENT_ID']));

                $this->domXml->create_element('name', $view['NAME']);

                $this->domXml->go_to_parent();
            }
        }
    }


    private function getCartItem($item_id, $data, $curr_info)
    {
        $item = $this->work_model->getItemInfo($item_id);

        $item['sh_disc_img_small'] = '';
        $item['sh_disc_img_big'] = '';
        $item['has_discount'] = 0;

        list($new_price, $new_price1) = $this->work_model->recountPrice($item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->params['currency'], $curr_info['PRICE']);

        if ($this->params['currency'] > 1) {
            $item['iprice'] = round($new_price, 1);
            $item['iprice1'] = round($new_price1, 1);
        } else {
            $item['iprice'] = round($new_price);
            $item['iprice1'] = round($new_price1);
        }

        $item = $this->recountPrice($item);

        $resul_price = ($item['iprice1'] > 0) ? $item['iprice1'] : $item['iprice'];

        $this->domXml->create_element('item', '', 2);
        $this->domXml->set_attribute(array('id' => $item_id,
            'price' => $item['iprice'],
            'price1' => $item['iprice1'],
            'real_price' => $item['PRICE'],
            'real_price1' => $item['PRICE1'],
            'has_discount' => $item['has_discount'],
            'count' => $data['count'],
            'total_price' => ceil($data['count'] * $resul_price),
        ));

        $href = $this->lang . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';

        $this->domXml->create_element('name', $item['NAME']);
        $this->domXml->create_element('brand_name', $item['BRAND_NAME']);
        $this->domXml->create_element('short_description', nl2br($item['DESCRIPTION']));
        $this->domXml->create_element('sname', $curr_info['SNAME']);
        $this->domXml->create_element('nat_sname', $item['SNAME']);

        $this->domXml->create_element('href', $href);

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

        if (!empty($item['DISCOUNTS_IMAGE']) && strchr($item['DISCOUNTS_IMAGE'], "#")) {
            $tmp = explode('#', $item['DISCOUNTS_IMAGE']);
            $this->domXml->create_element('discount_image', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                'w' => $tmp[1],
                'h' => $tmp[2]
            ));
            $this->domXml->go_to_parent();
        }

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

        $this->domXml->go_to_parent();

        return $resul_price;
    }

}