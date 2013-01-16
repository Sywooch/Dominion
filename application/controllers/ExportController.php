<?php
  class ExportController extends Zend_Controller_Action {    
    
    public $Catalogue;
    public $Cart;
    public $domXml;
    public $doc_id;
    public $currency_rate;
    public $currency;
    public $curr_info;
    public $action;
    public $baseUrl;
    
    public $tr = array(
   "Г"=>"G","Ё"=>"YO","Є"=>"E","Ю"=>"YI","Я"=>"I",
   "и"=>"i","г"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
   "ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
   "Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
   "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
   "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
   "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
   "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"'","Ы"=>"YI","Ь"=>"",
   "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
   "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
   "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
   "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"'",
   "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"," "=>'-',
   "…" => "-" ,"і" => "i","®" => "-" ,"І" => "I",
   "«" => "-","»" => "-","(" => "",")" => ""
  );
    
    function init(){ 
      define('SITE_PATH', $_SERVER['DOCUMENT_ROOT']);
      
      Zend_Loader::loadClass('Catalogue');
      Zend_Loader::loadClass('Cart');
      
      $this->Catalogue = new Catalogue();
      $this->Cart = new Cart();
      
      $this->domXml = new DomXML('1.0', 'UTF-8', false);
      
      $this->action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
      
      $this->doc_id = $this->Catalogue->getPriceIndentId($this->action); 
                                       
      $this->currency_rate = $this->Catalogue->getCurrencyRate(); 
      $this->currency = $this->Catalogue->getCurrencyId('UAH'); 
      
      $this->curr_info = $this->Cart->getCurrencyInfo($this->currency); 
      
      $this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/';
    }

    public function priceuaAction(){ 
      $this->domXml->create_element('price',"",1);
      $this->domXml->set_attribute(array('date' => date("Y-m-d 00:17")));
      
      $this->domXml->create_element('name','Доминион',1);    
      
      $this->domXml->create_element('currency','',1);
      $this->domXml->set_attribute(array('code' => 'UAH'
                                       , 'rate' => 1));
      $this->domXml->go_to_parent();
      
      $catalog = $this->Catalogue->getAllCats();      
      if(!empty($catalog)){
        $this->domXml->create_element('catalog','',1);
        foreach($catalog as $cat){
          $this->domXml->create_element('category',$cat['NAME'],2);
          $this->domXml->set_attribute(array('id' => $cat['CATALOGUE_ID']
                                            ,'parentId' => $cat['PARENT_ID']));
                                            
          $this->domXml->go_to_parent();
          
        }
        $this->domXml->go_to_parent();
      }

      $catalogs = $this->Catalogue->getExportCatalog($this->doc_id);
      if(!empty($catalogs)){
        $this->domXml->create_element('items','',2);
        foreach($catalogs as $cat){
          $items = $this->Catalogue->getExportItems($cat['CATALOGUE_ID']);
          if(!empty($items)){            
            foreach($items as $itm){
              $urname = $this->getUrName($itm);
              $url = $this->baseUrl."item/".$itm['ITEM_ID']."/".$urname."/";
              $currency = $itm['SYSTEM_NAME'];
              
              list($new_price, $new_price1) = $this->Cart->recountPrice($itm['ITEM_PRICE'],$itm['ITEM_PRICE1'],$itm['CURRENCY_ID'],$this->currency,$this->curr_info['PRICE']);
              
              if($this->currency > 1){
                 $price = round($new_price,1);
                 $price1 = round($new_price1,1);
              }
              else{
                 $price = round($new_price);
                 $price1 = round($new_price1);
              }
              
              if($itm['IMAGE1']){
               $IMAGE=split("#",$itm['IMAGE1']);
               $imageUrl = $this->baseUrl."images/it/".$IMAGE[0];
              }
              else $imageUrl='';
              
              $this->domXml->create_element('item','',2);
              $this->domXml->set_attribute(array('id' => $itm['ITEM_ID']));
              $this->domXml->create_element('name',$itm['TYPENAME'].' '.$itm['BRAND_NAME'].' '.$itm['NAME']);
              $this->domXml->create_element('categoryId',$itm['CATALOGUE_ID']);
              $this->domXml->create_element('price',$price);
              $this->domXml->create_element('url',$url);
              $this->domXml->create_element('image',$imageUrl);
              $this->domXml->create_element('vendor',$itm['BRAND_NAME']);
              $this->domXml->create_element('description',strip_tags($itm['DESCRIPTION']));
              $this->domXml->create_element('warranty','');              
              
              $this->domXml->go_to_parent();
            }
          }
        }
        $this->domXml->go_to_parent();
      }
      
      $this->saveFileExport();
    }
    
    public function yandexAction(){ 
      $this->yandexCatalogExport();
      $this->saveFileExport();
    }
    
    public function pnAction(){ 
      $this->catalogExport();
      $this->saveFileExport();
    }
    
    public function ecatalogAction(){ 
      $this->catalogExport();
      $this->saveFileExport();
    }
    
    private function yandexCatalogExport(){
      $this->domXml->create_element('yml_catalog',"",1);
      $this->domXml->set_attribute(array('date' => date("Y-m-d 00:17")));
      
      $this->domXml->create_element('shop','',2);
      
      $this->domXml->create_element('name','Доминион');
      $this->domXml->go_to_parent();
      $this->domXml->create_element('company','Доминион');
      $this->domXml->go_to_parent();
      $this->domXml->create_element('url',$this->baseUrl);
      $this->domXml->go_to_parent();
                                              
      $this->domXml->create_element('currencies','',2);
      $this->domXml->create_element('currency','',2);
      $this->domXml->set_attribute(array('id' => 'UAH'
                                       , 'rate' => 1));
      
      $this->domXml->set_tag('//shop', true);
      
      $catalog = $this->Catalogue->getAllCats();      
      if(!empty($catalog)){
        $this->domXml->create_element('categories','',2);
        foreach($catalog as $cat){
          $this->domXml->create_element('category',$cat['NAME'],2);
          $this->domXml->set_attribute(array('id' => $cat['CATALOGUE_ID']
                                            ,'parentId' => $cat['PARENT_ID']));
                                            
          $this->domXml->go_to_parent();
          
        }
        $this->domXml->set_tag('//shop', true);
      }

      $catalogs = $this->Catalogue->getExportCatalog($this->doc_id);
      if(!empty($catalogs)){
        $this->domXml->create_element('offers','',2);
        foreach($catalogs as $cat){
          $items = $this->Catalogue->getExportItems($cat['CATALOGUE_ID']);
          if(!empty($items)){            
            foreach($items as $itm){
              $urname = $this->getUrName($itm);
              $url = $this->baseUrl."item/".$itm['ITEM_ID']."/".$urname."/";
              $currency = $itm['SYSTEM_NAME'];
              
              list($new_price, $new_price1) = $this->Cart->recountPrice($itm['ITEM_PRICE'],$itm['ITEM_PRICE1'],$itm['CURRENCY_ID'],$this->currency,$this->curr_info['PRICE']);
              
              if($this->currency > 1){
                 $price = round($new_price,1);
                 $price1 = round($new_price1,1);
              }
              else{
                 $price = round($new_price);
                 $price1 = round($new_price1);
              }
              
              if($itm['IMAGE1']){
               $IMAGE=split("#",$itm['IMAGE1']);
               $imageUrl = $this->baseUrl."images/it/".$IMAGE[0];
              }
              else $imageUrl='';
              
              $name = $itm['BRAND_NAME'].' '.$itm['NAME'];
              
              $this->domXml->create_element('offer','',2);
              $this->domXml->set_attribute(array('id' => $itm['ITEM_ID']
                                                ,'type'=>'vendor.model'
                                                ,'available' => 'true'));
              $this->domXml->create_element('url',$url);
              $this->domXml->create_element('price',$price);
              $this->domXml->create_element('currencyId','UAH');
              $this->domXml->create_element('categoryId',$itm['CATALOGUE_ID']);
              $this->domXml->create_element('picture',$imageUrl);
//              $this->domXml->create_element('name',$name);
              $this->domXml->create_element('typePrefix',$itm['TYPENAME']);
              $this->domXml->create_element('vendor',$itm['BRAND_NAME']);
              $this->domXml->create_element('model',$itm['NAME']);
              $this->domXml->create_element('description',strip_tags($itm['DESCRIPTION']));              
              $this->domXml->go_to_parent();
            }
          }
        }
        
        $this->domXml->go_to_parent();
      }
    }
    
    private function catalogExport(){
      $this->domXml->create_element('yml_catalog',"",1);
      $this->domXml->set_attribute(array('date' => date("Y-m-d 00:17")));
      
      $this->domXml->create_element('shop','',2);
      
      $this->domXml->create_element('name','Доминион');
      $this->domXml->go_to_parent();
      $this->domXml->create_element('company','Доминион');
      $this->domXml->go_to_parent();
      $this->domXml->create_element('url',$this->baseUrl);
      $this->domXml->go_to_parent();
                                              
      $this->domXml->create_element('currencies','',2);
      $this->domXml->create_element('currency','',2);
      $this->domXml->set_attribute(array('id' => 'UAH'
                                       , 'rate' => 1));
      
      $this->domXml->set_tag('//shop', true);
      
      $catalog = $this->Catalogue->getAllCats();      
      if(!empty($catalog)){
        $this->domXml->create_element('categories','',2);
        foreach($catalog as $cat){
          $this->domXml->create_element('category',$cat['NAME'],2);
          $this->domXml->set_attribute(array('id' => $cat['CATALOGUE_ID']
                                            ,'parentID' => $cat['PARENT_ID']));
                                            
          $this->domXml->go_to_parent();
          
        }
        $this->domXml->set_tag('//shop', true);
      }

      $catalogs = $this->Catalogue->getExportCatalog($this->doc_id);
      if(!empty($catalogs)){
        $this->domXml->create_element('offers','',2);
        foreach($catalogs as $cat){
          $items = $this->Catalogue->getExportItems($cat['CATALOGUE_ID']);
          if(!empty($items)){            
            foreach($items as $itm){
              $urname = $this->getUrName($itm);  
              $url = $this->baseUrl."item/".$itm['ITEM_ID']."/".$urname."/";
              $currency = $itm['SYSTEM_NAME'];
              
              list($new_price, $new_price1) = $this->Cart->recountPrice($itm['ITEM_PRICE'],$itm['ITEM_PRICE1'],$itm['CURRENCY_ID'],$this->currency,$this->curr_info['PRICE']);
              
              if($this->currency > 1){
                 $price = round($new_price,1);
                 $price1 = round($new_price1,1);
              }
              else{
                 $price = round($new_price);
                 $price1 = round($new_price1);
              }
              
              if($itm['IMAGE1']){
               $IMAGE=split("#",$itm['IMAGE1']);
               $imageUrl = $this->baseUrl."images/it/".$IMAGE[0];
              }
              else $imageUrl='';
              
              $this->domXml->create_element('offer','',2);
              $this->domXml->set_attribute(array('id' => $itm['ITEM_ID']                                                
                                                ,'available' => 'true'));
              $this->domXml->create_element('name',$itm['TYPENAME'].' '.$itm['BRAND_NAME'].' '.$itm['NAME']);
              $this->domXml->create_element('categoryId',$itm['CATALOGUE_ID']);
              $this->domXml->create_element('currencyId','UAH');
//              $this->domXml->create_element('currencyId',$currency);
              $this->domXml->create_element('price',$price);
              $this->domXml->create_element('url',$url);
              $this->domXml->create_element('picture',$imageUrl);
              $this->domXml->create_element('vendor',$itm['BRAND_NAME']);
//              $this->domXml->create_element('description',$itm['DESCRIPTION']);
              $this->domXml->create_element('warranty','');              
              
              $this->domXml->go_to_parent();
            }
          }
        }
        
        $this->domXml->go_to_parent();
      }
    }
    
    private function saveFileExport(){
//      $dir_path = SITE_PATH.'/'.$this->action;
//    
//      if(!is_dir($dir_path)){
//        mkdir($dir_path, 0755);
//        chmod($dir_path, 0755);
//      }
      
//      if(is_dir($dir_path)){
        $file_path = SITE_PATH.'/price_export/'.$this->action.'.xml';
        $xml = $this->domXml->getXML();
        $xml = $this->removeHTMLentity($xml);
        file_put_contents($file_path, $xml);
        
//        $this->domXml->saveXML($file_path);
//      }
      exit;
    }
    
    private function getUrName($item) {
      $item['NAME'] = trim($item['NAME']);            
      $urname = preg_replace('/[^\w]/', '-', $this->translit($item['NAME']));
      $urname = preg_replace("/-{2,}/","-",$urname);
      
      return $urname;
    }
    
    private function translit($cyr_str) {
      return strtr($cyr_str,$this->tr);
    }
    
    private function removeHTMLentity($xml){
      $search = array (
                          "'&nbsp;'i","'&amp;'i","'&quot;'i","'&cent;'i","'&euro;'i","'&pound;'i","'&yen;'i","'&copy;'i","'&reg;'i","'&trade;'i",
                          "'&permil;'i","'&micro;'i","'&middot;'i","'&bull;'i","'&hellip;'i","'&prime;'i","'&Prime;'i","'&sect;'i","'&para;'i","'&szlig;'i",
                          "'&lsaquo;'i","'&rsaquo;'i","'&laquo;'i","'&raquo;'i","'&lsquo;'i","'&rsquo;'i","'&ldquo;'i","'&rdquo;'i","'&sbquo;'i","'&bdquo;'i",
                          "'&lt;'i","'&gt;'i","'&le;'i","'&ge;'i","'&ndash;'i","'&mdash;'i","'&macr;'i","'&oline;'i","'&curren;'i","'&brvbar;'i",
                          "'&uml;'i","'&iexcl;'i","'&iquest;'i","'&circ;'i","'&tilde;'i","'&deg;'i","'&minus;'i","'&plusmn;'i","'&divide;'i","'&frasl;'i",
                          "'&times;'i","'&sup1;'i","'&sup2;'i","'&sup3;'i","'&frac14;'i","'&frac12;'i","'&frac34;'i","'&fnof;'i","'&int;'i","'&sum;'i",
                          "'&infin;'i","'&radic;'i","'&sim;'i","'&cong;'i","'&asymp;'i","'&ne;'i","'&equiv;'i","'&isin;'i","'&notin;'i","'&ni;'i",
                          "'&prod;'i","'&and;'i","'&or;'i","'&not;'i","'&cap;'i","'&cup;'i","'&part;'i","'&forall;'i","'&exist;'i","'&empty;'i",
                          "'&nabla;'i","'&lowast;'i","'&prop;'i","'&ang;'i","'&acute;'i","'&cedil;'i","'&ordf;'i","'&ordm;'i","'&dagger;'i","'&Dagger;'i",
                          "'&Agrave;'i","'&Aacute;'i","'&Acirc;'i","'&Atilde;'i","'&Auml;'i","'&Aring;'i","'&AElig;'i","'&Ccedil;'i","'&Egrave;'i","'&Eacute;'i",
                          "'&Ecirc;'i","'&Euml;'i","'&Igrave;'i","'&Iacute;'i","'&Icirc;'i","'&Iuml;'i","'&ETH;'i","'&Ntilde;'i","'&Ograve;'i","'&Oacute;'i",
                          "'&Ocirc;'i","'&Otilde;'i","'&Ouml;'i","'&Oslash;'i","'&OElig;'i","'&Scaron;'i","'&Ugrave;'i","'&Uacute;'i","'&Ucirc;'i","'&Uuml;'i",
                          "'&Yacute;'i","'&Yuml;'i","'&THORN;'i","'&agrave;'i","'&aacute;'i","'&acirc;'i","'&atilde;'i","'&auml;'i","'&aring;'i","'&aelig;'i",
                          "'&ccedil;'i","'&egrave;'i","'&eacute;'i","'&ecirc;'i","'&euml;'i","'&igrave;'i","'&iacute;'i","'&icirc;'i","'&iuml;'i","'&eth;'i",
                          "'&ntilde;'i","'&ograve;'i","'&oacute;'i","'&ocirc;'i","'&otilde;'i","'&ouml;'i","'&oslash;'i","'&oelig;'i","'&scaron;'i","'&ugrave;'i",
                          "'&uacute;'i","'&ucirc;'i","'&uuml;'i","'&yacute;'i","'&thorn;'i","'&yuml;'i","'&Alpha;'i","'&Beta;'i","'&Gamma;'i","'&Delta;'i",
                          "'&Epsilon;'i","'&Zeta;'i","'&Eta;'i","'&Theta;'i","'&Iota;'i","'&Kappa;'i","'&Lambda;'i","'&Mu;'i","'&Nu;'i","'&Xi;'i",
                          "'&Omicron;'i","'&Pi;'i","'&Rho;'i","'&Sigma;'i","'&Tau;'i","'&Upsilon;'i","'&Phi;'i","'&Chi;'i","'&Psi;'i","'&Omega;'i",
                          "'&alpha;'i","'&beta;'i","'&gamma;'i","'&delta;'i","'&epsilon;'i","'&zeta;'i","'&eta;'i","'&theta;'i","'&iota;'i","'&kappa;'i",
                          "'&lambda;'i","'&mu;'i","'&nu;'i","'&xi;'i","'&omicron;'i","'&pi;'i","'&rho;'i","'&sigmaf;'i","'&sigma;'i","'&tau;'i",
                          "'&upsilon;'i","'&phi;'i","'&chi;'i","'&psi;'i","'&omega;'i","'&alefsym;'i","'&piv;'i","'&real;'i","'&thetasym;'i","'&upsih;'i",
                          "'&weierp;'i","'&image;'i","'&larr;'i","'&uarr;'i","'&rarr;'i","'&darr;'i","'&harr;'i","'&crarr;'i","'&lArr;'i","'&uArr;'i",
                          "'&rArr;'i","'&dArr;'i","'&hArr;'i","'&there4;'i","'&sub;'i","'&sup;'i","'&nsub;'i","'&sube;'i","'&supe;'i","'&oplus;'i",
                          "'&otimes;'i","'&perp;'i","'&sdot;'i","'&lceil;'i","'&rceil;'i","'&lfloor;'i","'&rfloor;'i","'&lang;'i","'&rang;'i","'&loz;'i",
                          "'&spades;'i","'&clubs;'i","'&hearts;'i","'&diams;'i","'&ensp;'i","'&emsp;'i","'&thinsp;'i","'&zwnj;'i","'&zwj;'i","'&lrm;'i",
                          "'&rlm;'i","'&shy;'i"
                         );

        $replace = array (
                          "&#160;","&#38;","&#34;","&#162;","&#8364;","&#163;","&#165;","&#169;","&#174;","&#8482;",
                          "&#8240;","&#181;","&#183;","&#8226;","&#8230;","&#8242;","&#8243;","&#167;","&#182;","&#223;",
                          "&#8249;","&#8250;","&#171;","&#187;","&#8216;","&#8217;","&#8220;","&#8221;","&#8218;","&#8222;",
                          "&#60;","&#62;","&#8804;","&#8805;","&#8211;","&#8212;","&#175;","&#8254;","&#164;","&#166;",
                          "&#168;","&#161;","&#191;","&#710;","&#732;","&#176;","&#8722;","&#177;","&#247;","&#8260;",
                          "&#215;","&#185;","&#178;","&#179;","&#188;","&#189;","&#190;","&#402;","&#8747;","&#8721;",
                          "&#8734;","&#8730;","&#8764;","&#8773;","&#8776;","&#8800;","&#8801;","&#8712;","&#8713;","&#8715;",
                          "&#8719;","&#8743;","&#8744;","&#172;","&#8745;","&#8746;","&#8706;","&#8704;","&#8707;","&#8709;",
                          "&#8711;","&#8727;","&#8733;","&#8736;","&#180;","&#184;","&#170;","&#186;","&#8224;","&#8225;",
                          "&#192;","&#193;","&#194;","&#195;","&#196;","&#197;","&#198;","&#199;","&#200;","&#201;",
                          "&#202;","&#203;","&#204;","&#205;","&#206;","&#207;","&#208;","&#209;","&#210;","&#211;",
                          "&#212;","&#213;","&#214;","&#216;","&#338;","&#352;","&#217;","&#218;","&#219;","&#220;",
                          "&#221;","&#376;","&#222;","&#224;","&#225;","&#226;","&#227;","&#228;","&#229;","&#230;",
                          "&#231;","&#232;","&#233;","&#234;","&#235;","&#236;","&#237;","&#238;","&#239;","&#240;",
                          "&#241;","&#242;","&#243;","&#244;","&#245;","&#246;","&#248;","&#339;","&#353;","&#249;" ,
                          "&#250;","&#251;","&#252;","&#253;","&#254;","&#255;","&#913;","&#914;","&#915;","&#916;",
                          "&#917;","&#918;","&#919;","&#920;","&#921;","&#922;","&#923;","&#924;","&#925;","&#926;",
                          "&#927;","&#928;","&#929;","&#931;","&#932;","&#933;","&#934;","&#935;","&#936;","&#937;",
                          "&#945;","&#946;","&#947;","&#948;","&#949;","&#950;","&#951;","&#952;","&#953;","&#954;",
                          "&#955;","&#956;","&#957;","&#958;","&#959;","&#960;","&#961;","&#962;","&#963;","&#964;",
                          "&#965;","&#966;","&#967;","&#968;","&#969;","&#8501;","&#982;","&#8476;","&#977;","&#978;",
                          "&#8472;","&#8465;","&#8592;","&#8593;","&#8594;","&#8595;","&#8596;","&#8629;","&#8656;","&#8657;",
                          "&#8658;","&#8659;","&#8660;","&#8756;","&#8834;","&#8835;","&#8836;","&#8838;","&#8839;","&#8853;",
                          "&#8855;","&#8869;","&#8901;","&#8968;","&#8969;","&#8970;","&#8971;","&#9001;","&#9002;","&#9674;",
                          "&#9824;","&#9827;","&#9829;","&#9830;","&#8194;","&#8195;","&#8201;","&#8204;","&#8205;","&#8206;",
                          "&#8207;","&#173;"
                          );

        $xml = preg_replace ($search, $replace, $xml);

        return $xml;
      }
    
  }