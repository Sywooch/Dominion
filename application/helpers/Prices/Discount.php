<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 06.06.13
 * Time: 13:03
 * To change this template use File | Settings | File Templates.
 */
/**
 * Class calculate discount for some strategy
 *
 * Class Helpers_Prices_Discount
 */
class Helpers_Prices_Discount extends App_Controller_Helper_HelperAbstract
{
    /**
     * Calculate discount for prices
     *
     * @param array $item
     *
     * @return mixed
     */
    public function calcDiscount($item)
    {
        $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');

        $ct_helper = $helperLoader->loadHelper('Cart', $this->params['currency']);
        $ct_helper->setModel($this->work_model);

        return $ct_helper->recountPrice($item);
    }
}