<?php
class export_items{
  private $cmf;
  
  private $csvfile;
  
  public function __construct($cmf){
    $this->cmf = $cmf;    
  }
  
  public function run(){
    $this->getItem();
  }
  
  private function getItem(){
    
//    $colNames = array(iconv('utf-8','windows-1251','Группа')
//                    ,iconv('utf-8','windows-1251','Статус')
//                    );
//                    
//    $this->csvfile = implode(';', $colNames);
    $this->csvfile = '';
    
    $items = $this->getSqlItems();
    if(!empty($items)){         
      foreach($items as $key=>$cat){
        $column = $key + 1;
        $url = 'quot;';
        
        $pattern[0] = '/&amp;/';
        $pattern[1] = '/amp;/';
        $pattern[2] = '/&quot;/';
        $pattern[3] = '/&#039;/';
        
        $replace[0] = '&';
        $replace[1] = '';
        $replace[2] = '';
        $replace[3] = '';
        
        $cat['NAME'] = trim($cat['NAME']);
        $cat['NAME'] = preg_replace($pattern, $replace, $cat['NAME']);
        
        $urname = preg_replace('/[^\w]/', '-', $this->cmf->translit($cat['NAME']));
        $urname = preg_replace("/-{2,}/","-",$urname);
        
        $_item_href = 'http://7560000.com.ua/item/'.$cat['ITEM_ID'].'/'.$urname.'/';
        
        $cat['NAME'] = mb_convert_encoding($cat['NAME'], 'windows-1251', 'utf-8');
        if($cat['STATUS']==0 || $cat['PRICE']==0){
          $_status = 'Paused';
        }
        else{
          $_status = 'Active';
        }
        
        $colNames = array($cat['BRAND_NAME'].' '.$cat['NAME']
                         ,$_status
                         ,$_item_href
                         );
        
        if($key > 0) $this->csvfile.= "\r\n";
        $this->csvfile.= implode(';', $colNames);
        
      }
    }
  }
  
  public function getFile(){
    header('Content-type: application/octet-stream');
    header('Accept-Ranges: bytes');                     
    header('Content-Length: '.strlen($this->csvfile)); 
    header('Content-Disposition: attachment; filename="item_export.csv"');
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");
    echo $this->csvfile;
    exit;
  }
 
  private function getSqlItems(){
    $sql="select I.NAME 
               , I.ITEM_ID 
               , I.PRICE 
               , I.STATUS 
               , B.NAME as BRAND_NAME 
          from ITEM I
             , CATALOGUE C
             , BRAND B
          where C.IN_ADV = 1
          and I.CATALOGUE_ID = C.CATALOGUE_ID
          and I.BRAND_ID = B.BRAND_ID";
          
    return $this->cmf->select($sql);
  }
}
?>