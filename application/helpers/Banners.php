<?php
class Helpers_Banners extends App_Controller_Helper_HelperAbstract{

  public function getBanners($section_name, $align,$section){
    $banners = $this->work_model->getBanners($align,$section);

    if($banners){
      foreach($banners as $banner){
        $this->bannerNode($section_name, $banner);
      }
    }
  }
  
  public function getCatalogueBanner($catalogue_id){
    $Catalogue = new models_Catalogue();
    
    $banners = $Catalogue->getCatalogueBanner($catalogue_id);
    
    if($banners){
      foreach($banners as $banner){
        $this->bannerNode('banner_right', $banner);
      }
    }
  }
  
  private function bannerNode($section_name, $banner){
    $this->domXml->create_element($section_name,'',2);
    $this->domXml->set_attribute(array('section_align_id'  => $banner['SECTION_ALIGN_ID']
                                      ,'type' => $banner['TYPE']
                                      ,'newwin' => $banner['NEWWIN']
                                       ));

    $this->domXml->create_element('alt',$banner['ALT']);        

    $this->domXml->create_element('url',$banner['URL']);
    $this->domXml->create_element('burl',$this->bannerURL($banner['URL']));
    
    if($banner['IMAGE1']!='' && strchr($banner['IMAGE1'],"#")){
      $image = explode('#', $banner['IMAGE1']);
      $this->domXml->create_element('image','',2);

      $this->domXml->set_attribute(array('src'  => $image[0],
                                         'w'    => $image[1],
                                         'h'    => $image[2]
                                         ));
      $this->domXml->go_to_parent();
    }
    
    $this->setXmlNode($banner['DESCRIPTION'], 'description');

    if(!empty($banner['BANNER_CODE'])){
      $this->domXml->create_element('banner_code',$banner['BANNER_CODE'],0, array(), 1);
//      $this->setXmlNode($banner['BANNER_CODE'], 'banner_code');  
    }

    $this->domXml->go_to_parent();
    
  }
  
  private function bannerURL($url){
    if(!empty($url) || strchr($url,"http:")) $burl = $url;
    else{
      if(!empty($url)){
        if(strchr($url,"doc")){
        if(substr($url,0,1) != "/") $burl .= "/";
          $burl .= $url;
        }
        else{
          if(substr($url,0,1) != "/") $burl = "/doc/".$url;
          else $burl = "/doc".$url;
        }
        if(substr($url,-1) != "/") $burl .="/";
      } else $burl = '';
    }

    if(!empty($burl)) $url = $burl;
    else $url = '';

    return $url;
  }
}
