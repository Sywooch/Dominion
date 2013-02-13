<?php

class Helpers_Register extends App_Controller_Helper_HelperAbstract
{

    public function getMyData($member_id)
    {
        $members = $this->work_model->sigin_id($member_id);
        if (!empty($members)) {
            $this->domXml->create_element('member_data', '', 2);

            foreach ($members as $key => $view) {
                $key = strtolower($key);
                $this->domXml->create_element($key, $view);
            }
            $this->domXml->go_to_parent();
        }
    }

    public function getMyOrders($member_id)
    {
        $myorders = $this->work_model->getOrders($member_id);
        if (!empty($myorders)) {
            foreach ($myorders as $key => $view) {
                $curr_info = $this->params['Item']->getCurrencyInfo($this->params['currency']);

                $this->domXml->create_element('myorders', '', 2);
                $this->domXml->set_attribute(array('id' => $view['ZAKAZ_ID']
                    , 'order' => $key + 1
                ));

                $this->domXml->create_element('posted_at', $view['date']);
                $this->domXml->create_element('os_name', $view['ZS_NAME']);
                $this->domXml->create_element('os_color', $view['ZS_COLOR']);

                $total_sum = $this->getOrderDetails($view['ZAKAZ_ID']);

                $this->domXml->create_element('total_sum', $total_sum);
                $this->domXml->create_element('sname', $curr_info['SNAME']);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getMybonus($user_data)
    {
        $user_summ = 0;
        $discounts_id = $this->work_model->getUserDiscountId($user_data['user_id']);
        if (!empty($discounts_id)) {
            $present_discount = $this->work_model->getDiscountData($discounts_id);
            $this->createDiscountNode('present_discount', $present_discount);
        }
        $user_summ = $this->work_model->getUserOrderSumm($user_data['user_id']);
        $this->domXml->create_element('user_summ', $user_summ, 2);
        $this->domXml->go_to_parent();


        if (empty($present_discount['ORDERING']))
            $present_discount['ORDERING'] = 0;

        $next_discount_id = $this->work_model->getNextDiscountId($present_discount['ORDERING']);

        if (!empty($next_discount_id)) {
            $next_discount = $this->work_model->getDiscountData($next_discount_id['SHOPUSER_DISCOUNTS_ID']);
            $this->createDiscountNode('next_discount', $next_discount);
        }
    }

    private function createDiscountNode($node, $data)
    {
        $this->domXml->create_element($node, '', 2);
        $this->domXml->set_attribute(array('id' => $data['SHOPUSER_DISCOUNTS_ID'],
            'min' => $data['MIN'],
            'max' => $data['MAX'],
        ));

        if (!empty($data['IMAGE1'])) {
            $tmp = explode('#', $data['IMAGE1']);
            $this->domXml->create_element('image_small', '', 2);
            $this->domXml->set_attribute(array('src' => $tmp[0],
                'w' => $tmp[1],
                'h' => $tmp[2]
                    )
            );
            $this->domXml->go_to_parent();
        }

        $this->domXml->create_element('name', $data['NAME']);
        $this->domXml->create_element('color', $data['COLOR']);
        $this->domXml->go_to_parent();
    }

    private function getOrderDetails($id)
    {
        $items = $this->work_model->getZakazItems($id);
        $total = 0;

        if (!empty($items)) {
            foreach ($items as $view) {
                $this->domXml->create_element('item', '', 2);
                $this->domXml->set_attribute(array('id' => $view['ITEM_ID'],
                    'price' => $view['PRICE'],
                    'total' => $view['COST'],
                    'count' => $view['QUANTITY'],
                ));

                $href = $view['CATALOGUE_REALCATNAME'] . $view['ITEM_ID'] . '-' . $view['CATNAME'] . '/';

                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('sname', $view['SNAME']);

                $this->domXml->create_element('href', $href);



                $total+= $view['COST'];


                $this->domXml->go_to_parent();
            }
        }

        return $total;
    }

}