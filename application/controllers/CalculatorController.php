<?php
class CalculatorController extends App_Controller_Frontend_Action
{

    private $item_id;


    function init()
    {
//        parent::init();

//        $this->work_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
//        $this->work_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        /** @var DomXML domXml */
        $this->domXml = $this->view->serializer;

        $this->domXml->create_element('page',"",1);
        $this->domXml->set_tag('//page', true);

//        $this->currency = 1;

//        $this->getHelpers();
//        $this->getAuthSession();
//        $this->getCart();

        $this->template = "calculator/credit_calculator.xsl";
        $Item = new models_Item();
//        $Catalogue = new models_Catalogue();
//
//        $this->item_id = $this->_getParam('id');
//        $catalogue_id = $Item->getItemCatalog($this->item_id);
//        $parent_active = $Catalogue->getatalogActive($catalogue_id);
//        if ($this->item_id == 0 || !$parent_active) {
//            $this->page_404();
//        }
//
//        $res = '';
//        if (!empty($this->item_id)) $res = $Item->getItemInfo($this->item_id);
//
//        if ($this->item_id === false || ($this->item_id > 0 && empty($res))) {
//            $this->page_404();
//        }
    }

    public function getAction()
    {
//        $Article = new models_Article();


        $itemId = $this->_getParam('item_id');
        $priceItem = $this->_getParam('item_id');

        $Item = new models_Item();
        $this->domXml->create_element('credit_calculator','',1, array('bank' => 'renesans', 'price' => 23424));



    }
}