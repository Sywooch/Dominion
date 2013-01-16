<?php
  class CompareController extends App_Controller_Frontend_Action {    
    
    function init(){    
      parent::init();
    }

    public function indexAction(){ 
      $catalogue_id = $this->_getParam('id');
      
      $AnotherPages = new models_AnotherPages();
      $Item = new models_Item();
      
      $doc_id = $AnotherPages->getDocByUrl('/compare/');
      
      $o_data = array('id' => $doc_id,
                      'is_compare' => 1
                     );
                     
      $this->openData($o_data);      
      
      $ap_helper = $this->_helper->helperLoader('AnotherPages');      
      $ap_helper->setLang($this->lang, $this->lang_id);
      $ap_helper->setModel($AnotherPages);
      $ap_helper->setDomXml($this->domXml);
      $ap_helper->getDocInfo($doc_id);
      $this->domXml = $ap_helper->getDomXml();                      
      
      $params['currency'] = 1;
      
      $it_helper = $this->_helper->helperLoader('Compare', $params);      
      $it_helper->setLang($this->lang, $this->lang_id);
      $it_helper->setModel($Item);
      $it_helper->setDomXml($this->domXml);
      $it_helper->getComparedList($catalogue_id);        
      $this->domXml = $it_helper->getDomXml();
      
      $this->getDocPath($doc_id);
    }
  }