<?php

class member_discount
{

    private $Item;

    public function __construct()
    {
        Zend_Loader::loadClass('models_Item');

        $this->Item = new models_Item();
    }

    public function run()
    {
        $this->setUserDiscount();
    }

    private function getUserOrderSumm()
    {
        $result = array();

        $data = $this->Item->getUserOrders();
        if (!empty($data)) {
            foreach ($data as $view) {
                $summ = $this->Item->getUserOrderSumm($view['ZAKAZ_ID']);

                if (!isset($result[$view['USER_ID']])) {
                    $result[$view['USER_ID']] = 0;
                }

                if (!empty($summ)) {
                    $result[$view['USER_ID']] += $summ;
                }
            }
        }

        return $result;
    }

    /**
     * Расчитать скидку юзверям
     *
     */
    private function setUserDiscount()
    {

        $usersIdDiscounts = $this->getUserOrderSumm();

        if (empty($usersIdDiscounts)) {
            return false;
        }

        Zend_Loader::loadClass('models_Registration');

        $Registration = new models_Registration();

        // Сбрасываем всем дискаунт
        $Registration->resetUsersDiscount();

        // Иперсчитываем заново
        foreach ($usersIdDiscounts as $user_id => $summ) {
            $shopuser_discounts_id = $this->Item->getUserDiscountId($summ);
            if (!empty($shopuser_discounts_id)) {
                $update_data['SHOPUSER_DISCOUNTS_ID'] = $shopuser_discounts_id;
                $Registration->updateRegData($update_data, $user_id);
            }
        }
    }

}