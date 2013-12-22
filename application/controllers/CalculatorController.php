<?php
class CalculatorController extends App_Controller_Frontend_Action
{

    private $item_id;

    private $itemHelper;

    function init()
    {
//        parent::init();

//        $this->work_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
//        $this->work_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        /** @var DomXML domXml */
        $this->domXml = $this->view->serializer;

        $this->domXml->create_element('page', "", 1);
        $this->domXml->set_tag('//page', TRUE);

        $this->currency = 1;

//        $this->getHelpers();
//        $this->getAuthSession();
//        $this->getCart();

        $this->template = "calculator/credit_calculator.xsl";

        /** @var Helpers_Prices_Item $itemHelper */
        $this->itemHelper = $this->_helper->helperLoader("Prices_Item");

        $this->itemHelper->setModel(new models_Item());

    }

    public function getAction()
    {

        $itemId = $this->_getParam('item_id');

        $itemPrice = $this->itemHelper->getItemPrice($itemId, $this->currency);
        $itemPrice = $this->itemHelper->getCreditPrice($itemPrice);
        $this->domXml->create_element('credit_calculator', '', 1, array(
            'bank' => 'renesans',
            'price' => $itemPrice,
            'item_id' => $itemId
        ));

    }

    public function sendAction()
    {

        $itemId = $this->_getParam('item_id');

        $itemPrice = $this->itemHelper->getItemPrice($this->_getParam('item_id'), $this->currency);
        $itemPrice = $this->itemHelper->getCreditPrice($itemPrice);

    }
}