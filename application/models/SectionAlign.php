<?php
class models_SectionAlign extends ZendDBEntity{
  protected $_name = 'SECTION_ALIGN';
  
  public function GetBanns($where){     
    $sql="select ALIGN_ID
                ,IMAGE1
                ,TYPE
                ,ALT
                ,DESCRIPTION
                ,BANNER_CODE
                ,URL
                ,NEWWIN 
          from {$this->_name} 
          where BANN_SECTION_ID=? 
            and STATUS=1";
    
    return $this->_db->fetchAll($sql, array($where));
  }


  public function getBanners($align,$section){
    $sql = "select SECTION_ALIGN_ID,
                   IMAGE1,
                   ALT,
                   DESCRIPTION,
                   BANNER_CODE,
                   TYPE,
                   URL,
                   NEWWIN 
            from {$this->_name} 
            where ALIGN_ID={$align} 
              and BANN_SECTION_ID={$section}
              and STATUS=1
            order by ORDERING";
              
     return $this->_db->fetchAll($sql);
   }
   
   public function getRandomBanner($align,$section,$lang=0){
     $sql = "select SECTION_ALIGN_ID,
                    IMAGE1,
                    ALT,
                    DESCRIPTION,
                    BANNER_CODE,
                    TYPE,
                    URL,
                    NEWWIN 
             from {$this->_name} 
             where ALIGN_ID=? 
               and BANN_SECTION_ID=? 
               and STATUS=1 
             order by RAND() limit 0,1";
             
     $banner = $this->_db->fetchRow($sql, array($align,$section));

      $burl = '';
      if($banner['URL']!='' || strchr($banner['URL'],"http:")) $burl = $banner['URL'];
      else
      {
        if($banner['URL']!='')
        {
           if(strchr($banner['URL'],"doc"))
           {
              if(substr($banner['URL'],0,1) != "/") $burl .= "/";
              $burl .= $banner['URL'];
           }
           else
           {
              if(substr($banner['URL'],0,1) != "/") $burl = "/doc/".$banner['URL'];
              else $burl = "/doc".$banner['URL'];
           }
           if(substr($banner['URL'],-1) != "/") $burl .="/";
        } else $burl = '';
      }
      if($burl!='') $banner['burl'] = $burl;
      else $banner['burl'] = '';
      return $banner;
   }
}
?>