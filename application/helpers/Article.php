<?php
class Helpers_Article extends App_Controller_Helper_HelperAbstract{
  
  public function getArticleSingle($id){
    $news = $this->work_model->getArticleSingle($id, $this->lang_id);
    
    if(!empty($news)){
      $this->domXml->create_element('artcle_single','',2);
      $this->domXml->set_attribute(array('article_id'  => $news['ARTICLE_ID']));
      
      $this->domXml->create_element('name',$news['NAME']);
      $this->domXml->create_element('posted_at',$news['date']);
      $this->setXmlNode($news['LONG_TEXT'], 'txt');
      
      $this->domXml->go_to_parent();
    }
  }
  
  public function getMetaSingle($id){
    $news = $this->work_model->getArticleSingle($id, $this->lang_id);
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
    
    $doc_id = $AnotherPages->getDocByUrl('/article/');
    $name = $AnotherPages->getDocName($doc_id, $this->lang_id);
      
    $this->domXml->create_element('breadcrumbs','', 2);
    $this->domXml->set_attribute(array('id'  => $id
                            ));

    $href = $this->lang.'/article/';                            
    $this->domXml->create_element('name',$name);
    $this->domXml->create_element('url',$href);                                  
    $this->domXml->go_to_parent();
      
    $name = $this->work_model->getArticleName($id, $this->lang_id);
    if(!empty($name)){
      $this->domXml->create_element('breadcrumbs','', 2);
      $this->domXml->set_attribute(array('id'  => $id
                              ));
      
      $this->domXml->create_element('name',$name);
      $this->domXml->create_element('url','');                                  
      $this->domXml->go_to_parent();
    }
  }
  
  public function getArticles($startSelect, $pageSize){
    $news_list  = $this->work_model->getArticles($startSelect, $pageSize, $this->lang_id);
    if($news_list){
      foreach($news_list as $view){
        $this->domXml->create_element('artcle_list','',2);
        $this->domXml->set_attribute(array('article_id'  => $view['ARTICLE_ID']));
                
        $href = $this->lang.'/article/'.$view['CATNAME'].'/';
        
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
