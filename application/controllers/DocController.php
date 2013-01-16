<?php
  class DocController extends App_Controller_Frontend_Action {    
    
    public $doc_id = 0;
    
    function init(){ 
      parent::init();
      
      if(empty($this->work_action) || $this->work_action=='index'){
        $this->page_404();
      }      
    }

    public function viewAction(){
      $AnotherPages = new models_AnotherPages();
      $SectionAlign = new models_SectionAlign();
      
      $file_name = $this->_getParam('n');
      if(!empty($file_name)){
         $this->doc_id = $AnotherPages->getDocId($file_name);
      }
      
      $res = $AnotherPages->getDocInfo($this->doc_id);
      if($this->doc_id==0 || ($this->doc_id > 0 && empty($res))){
        $this->page_404();
      }
                     
      $o_data['id']= $this->doc_id;
      $o_data['is_slider']= 1;
      
      $this->openData($o_data);
      
      $ap_helper = $this->_helper->helperLoader('AnotherPages');      
      $ap_helper->setModel($AnotherPages);
      $ap_helper->setDomXml($this->domXml);
      $ap_helper->getDocPath($this->doc_id);
      $ap_helper->getDocInfo($this->doc_id);
      $this->domXml = $ap_helper->getDomXml();
      
      $bn_helper = $this->_helper->helperLoader('Banners');      
      $bn_helper->setModel($SectionAlign);
      $bn_helper->setDomXml($this->domXml);
      $bn_helper->getBanners('banner_right',15,17);
      $this->domXml = $bn_helper->getDomXml();
    }
    
    function socialAction(){
      $AnotherPages = new models_AnotherPages();
      $Item = new models_Item();
      
      $request = $this->getRequest();
      if($request->isGet()){
        $in = $request->getQuery('in');
        $url = $request->getQuery('url');
        $title = $request->getQuery('title');
        
        $social = $AnotherPages->getSocialsOne($in);
        if(!empty($social)){
          $pattern_item = '/item-(\d*)/';
          if(preg_match($pattern_item, $url, $out)){
            $item = $Item->getItemInfoForUrl($out[1]);
            $url = 'http://'.$_SERVER['HTTP_HOST'].$item['CATALOGUE_REALCATNAME'].$item['ITEM_ID'].'-'.$item['CATNAME'].'/';;
          }
          
          $social['URL'] = str_replace('&amp;', '&', $social['URL']);
          $soc_url = str_replace('##url##', $url, $social['URL']);
          $soc_url = str_replace('##title##', $title, $soc_url);
          
          $this->_redirect($soc_url);
        }
      }
    }    
  }
