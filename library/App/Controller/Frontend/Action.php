<?php

class App_Controller_Frontend_Action extends Zend_Controller_Action {

    public $template;
    public $work_controller;
    public $work_action;

    public $lang_id = 0;
    public $current_lang_id = 0;
    public $def_lang = '';
    public $lang = '';

    public $currency;
    /**
     * @var DomXML
     */
    public $domXml;

    public function init(){
      $this->work_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
      $this->work_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

      $this->domXml = $this->view->serializer;

      $this->domXml->create_element('page',"",1);
      $this->domXml->set_tag('//page', true);

      $this->domXml->create_element('default_title',$this->getSettingValue('def_html_title'), 1);
      $this->domXml->set_tag('//page', true);

      $this->currency = 1;

      $this->getHelpers();
      $this->getAuthSession();
      $this->getCart();
    }

    private function getHelpers(){
      $AnotherPages = new models_AnotherPages();
      $SectionAlign = new models_SectionAlign();
      $Textes = new models_Textes();

      $ap_helper = $this->_helper->helperLoader('AnotherPages');
      $ap_helper->setModel($AnotherPages);
      $ap_helper->setDomXml($this->domXml);
      $ap_helper->makeMenu(5);
      $this->domXml = $ap_helper->getDomXml();

      /* @var  $bn_helper Helpers_Banners*/
      $bn_helper = $this->_helper->helperLoader('Banners');
      $bn_helper->setModel($SectionAlign);
      $bn_helper->setDomXml($this->domXml);
      $bn_helper->getBanners('banner_top',1,1);
      $bn_helper->getBanners('banner_counters',14,16);
      $bn_helper->getBanners('banner_bottom',2,3);
      $bn_helper->getBanners('banner_socnets',16,18);
      $bn_helper->getBanners('banner_java_scripts',13,15);
      $this->domXml = $bn_helper->getDomXml();

      $tx_helper = $this->_helper->helperLoader('Textes');
      $tx_helper->setModel($Textes);
      $tx_helper->setDomXml($this->domXml);

      $tx_helper->getTextes('footer_page_left');
      $tx_helper->getTextes('footer_work');
      $tx_helper->getTextes('footer_phones');
      $tx_helper->getTextes('right_side_mobile');
      $tx_helper->getTextes('right_side_mobile_content');
      $tx_helper->getTextes('right_side_phone');

      $this->domXml = $tx_helper->getDomXml();
    }

    private function getCart(){
      $Item = new models_Item();

      $params['currency'] = $this->currency;

      $ct_helper = $this->_helper->helperLoader('Cart', $params);
      $ct_helper->setModel($Item);

      list($total_count, $total_summ) = $ct_helper->getBasket();

      $this->domXml->create_element('cart','',2);
      $this->domXml->create_element('total_summ',$total_summ);
      $this->domXml->create_element('total_count',$total_count);
      $this->domXml->go_to_parent();
    }

    private function getAuthSession(){
      $user_data = Zend_Auth::getInstance()->getIdentity();
      if(!empty($user_data)){
        $this->domXml->create_element('user_data','',2);

        $this->domXml->create_element('user_id',$user_data['user_id']);
        $this->domXml->create_element('user_name',$user_data['user_name']);
        $this->domXml->create_element('user_phone',$user_data['user_phone']);
        $this->domXml->create_element('user_email',$user_data['user_email']);

        $this->domXml->go_to_parent();
      }
    }

    public function postDispatch(){
      if($this->template){
        $template = $this->template;
      }
      else{
        $template =  $this->work_controller.'_'.$this->work_action.'.xsl';
      }

      $path_= $this->view->getScriptPath($template);
      if(!is_file($path_)){
        $path_ =  $this->work_controller.'.xsl';
      }
      else{
        $path_ = $template;
      }

      echo $this->view->render($path_);
    }

    public function openData($open_data){
      $this->domXml->create_element('data','',1);
      $this->domXml->set_attribute($open_data);
    }


    protected function sendJsonResponse($results)
    {
        $response = $this->getResponse();

        // send back a JSON response
        $response->clearAllHeaders();
        $response->clearBody();
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody(Zend_Json::encode($results));
    }

    protected function getSession()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        return $bootstrap->getResource('session');
    }

    public function getSettingValue($name){
      $SystemSets = new models_SystemSets();

      return $SystemSets->getSettingValue($name);
    }

    public function getTextes($name){
      $Textes = new models_Textes();

      return $Textes->getTextes($name, $this->lang_id);
    }

    public function getLangTextes($name){
      $Textes = new models_Textes();

      $result = $Textes->getTextes($name, $this->lang_id);
      return strip_tags($result);
    }

    public function page_404(){
      $this->template = 'error.xsl';

      $this->domXml->create_element('data','',1);
      $this->domXml->set_attribute(array('error'=>1));

      $this->getResponse()->setHttpResponseCode(404);
      $this->getResponse()->sendHeaders();

      $this->postDispatch();
    }

    public function getDocPath($id){
      $AnotherPages = new models_AnotherPages();

      $parent = $AnotherPages->getPath($id);
      if(!empty($parent)){
        foreach($parent as $view){
          $this->domXml->create_element('breadcrumbs','', 2);
          $this->domXml->set_attribute(array('id'  => $view['ANOTHER_PAGES_ID']
                                            ,'parent_id' =>  $view['PARENT_ID']
                                  ));

          $href='';
          if(!empty($view['url']) && (strpos('http://', $view['url'])!==false)){
            $href = $view['url'];
          }
          elseif(!empty($view['url']) && (strpos('http://', $view['url'])===false)){
            $href = $view['url'];
          }
          else $href = $this->lang.'/doc'.$view['REALCATNAME'];

          $this->domXml->create_element('name',$view['NAME']);
          $this->domXml->create_element('url',$href);
          $this->domXml->go_to_parent();
        }
      }
    }

}
