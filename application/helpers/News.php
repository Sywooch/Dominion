<?php
class Helpers_News extends App_Controller_Helper_HelperAbstract{
    
  public function getLastNews($amount){
    $result = $this->work_model->getNewsIndex($amount, $this->lang_id);
    
    if(!empty($result)){
      foreach($result as $view){        
        $this->domXml->create_element('news_block','',2);
        $this->domXml->set_attribute(array('news_id'  => $view['NEWS_ID']));
        
        $href = $this->lang.'/news/'.$view['CATNAME'].'/';
        
        $this->domXml->create_element('name',$view['NAME']);
        $this->domXml->create_element('posted_at',$view['DATA']);
        $this->domXml->create_element('short_description',$view['DESCRIPTION']);
        $this->domXml->create_element('href',$href);
        
        if(!empty($view['IMAGE1']) && strchr($view['IMAGE1'],"#")){
          $tmp=explode('#',$view['IMAGE1']);
          $this->domXml->create_element('image','',2);
          $this->domXml->set_attribute(array('src'  => $tmp[0],
                                       'w'    => $tmp[1],
                                       'h'    => $tmp[2]
                                       )
                                      );
          $this->domXml->go_to_parent();
        }
        
        $this->domXml->go_to_parent();
      }
    }
  }
  
  public function getNewsSingle($id){
    $news = $this->work_model->getNewsSingle($id, $this->lang_id);
    
    if(!empty($news)){
      $this->domXml->create_element('news_single','',2);
      $this->domXml->set_attribute(array('news_id'  => $news['NEWS_ID']));
      
      $this->domXml->create_element('name',$news['NAME']);
      $this->domXml->create_element('posted_at',$news['date']);
      $this->setXmlNode($news['LONG_TEXT'], 'txt');
      
      $this->domXml->go_to_parent();
    }
  }
  
  public function getMetaSingle($id){
    $news = $this->work_model->getNewsSingle($id, $this->lang_id);
    if(!empty($news)){        
      $this->domXml->create_element('docinfo','',2);  

      $this->domXml->create_element('title',$news['NAME']);
      $this->domXml->create_element('description',$news['NAME']);
      $this->domXml->create_element('keywords',$news['NAME']);

      $this->domXml->go_to_parent();
    }
  }
  
  public function getDocPath($id){
    $AnotherPages = new models_AnotherPages();
    
    $doc_id = $AnotherPages->getDocByUrl('/news/');
    $name = $AnotherPages->getDocName($doc_id, $this->lang_id);
      
    $this->domXml->create_element('breadcrumbs','', 2);
    $this->domXml->set_attribute(array('id'  => $id
                            ));

    $href = $this->lang.'/news/';                            
    $this->domXml->create_element('name',$name);
    $this->domXml->create_element('url',$href);                                  
    $this->domXml->go_to_parent();
      
    $name = $this->work_model->getNewsName($id, $this->lang_id);
    if(!empty($name)){
      $this->domXml->create_element('breadcrumbs','', 2);
      $this->domXml->set_attribute(array('id'  => $id
                              ));
      
      $this->domXml->create_element('name',$name);
      $this->domXml->create_element('url','');                                  
      $this->domXml->go_to_parent();
    }
  }
  
  public function getNews($startSelect, $pageSize){
    $news_list  = $this->work_model->getNews($startSelect, $pageSize, $this->lang_id);
    if($news_list){
      foreach($news_list as $view){
        $this->domXml->create_element('news_list','',2);
        $this->domXml->set_attribute(array('news_id'  => $view['NEWS_ID']));
                
        $href = $this->lang.'/news/'.$view['CATNAME'].'/';
        
        $this->domXml->create_element('name',$view['NAME']);
        $this->domXml->create_element('posted_at',$view['date']);
        $this->domXml->create_element('short_description',$view['DESCRIPTION']);
        $this->domXml->create_element('href',$href);
        
        if(!empty($view['IMAGE1']) && strchr($view['IMAGE1'],"#")){
          $tmp=explode('#',$view['IMAGE1']);
          $this->domXml->create_element('image','',2);
          $this->domXml->set_attribute(array('src'  => $tmp[0],
                                       'w'    => $tmp[1],
                                       'h'    => $tmp[2]
                                       )
                                      );
          $this->domXml->go_to_parent();
        }
                  
        $this->domXml->go_to_parent();
      }
    }
  }
}
