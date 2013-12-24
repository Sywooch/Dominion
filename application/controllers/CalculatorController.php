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

        $itemPrice = $this->itemHelper->getCreditPrice($this->itemHelper->getItemPrice($itemId, $this->currency));


        $creditCode = 'renesans';


        $creditModel = new models_Credit();
        $emailMessage = $creditModel->getEmailTemplate($creditCode);

        // Устанавливаем цену из базы - нет доверия тому что пришло  с сайта!
        $emailMessage = str_replace("##price##", $itemPrice, $emailMessage);

        foreach ($this->getAllParams() as $key => $value) {
            $emailMessage = str_replace("##{$key}##", $value, $emailMessage);
        }

        $itemModel = new models_Item();

        $item = $itemModel->getItemInfo($itemId);

        $emailMessage = str_replace("##item_name##", "{$item['TYPENAME']} {$item['BRAND_NAME']} {$item['NAME']}", $emailMessage);
        $emailMessage = str_replace("##url##", "{$item['CATALOGUE_REALCATNAME']}{$item['ITEM_ID']}-{$item['CATNAME']}/", $emailMessage);


        $emailMessage = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
            . $emailMessage . '</body></html>';

        $email = $creditModel->getManagerEmail($creditCode);
        $params['to'] = $email;
        $params['message'] = $emailMessage;

        $params['subject'] = "Новая заявка на кредит {$item['TYPENAME']} {$item['BRAND_NAME']} {$item['NAME']}";

        $email_from = $this->getSettingValue('email_from');
        $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
        preg_match_all($patrern, $email_from, $arr);

        $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
        $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

        // Отправляем сперва в банк
        App_Mail::send($params);

        // Send managers emails
        $params['to'] = explode(';', $this->getSettingValue('manager_emails'));

        // Теперь манагерам
        App_Mail::send($params);

    }
}