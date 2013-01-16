<?php
class Helpers_Socials extends App_Controller_Helper_HelperAbstract{
    
  public function getSocials($url, $title){
    $title = str_replace(' ','+', $title);
    $socials = $this->work_model->getSocials();
    if(!empty($socials)){
      foreach($socials as $val){
        $new_url = '';
        $this->domXml->create_element('socials','',2);
        $this->domXml->create_element('name',$val['NAME']);
        
//        $this->setXmlNode($val['NAME'],'name');

        if(!empty($url) && !empty($title)){
          $new_url = '/doc/social/?in='.$val['INDENT'].'&amp;url='.$url.'&amp;title='.$title;
          
          $this->domXml->create_element('url', $new_url);
        }
        
        if(!empty($val['IMAGE']) && strchr($val['IMAGE'],"#")){
           $tmp=explode('#',$val['IMAGE']);
           $this->domXml->create_element('image','',2);
           $this->domXml->set_attribute(array('src'  => $tmp[0]
                                              ,'w'   => $tmp[1]
                                              ,'h'   => $tmp[2]
                                               ));
           $this->domXml->go_to_parent();
        }
        
        $this->domXml->go_to_parent();
      }
    }
  }
}