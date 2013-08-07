<?php
defined('SITE_PATH')
    || define('SITE_PATH', realpath(dirname(__FILE__)).'/../');

  require ROOT_PATH."/lib/Translit.class.php";
  require ROOT_PATH."/lib/createSEFU_DB.class.php";

  define('SEFU_PREFIX_CAT_URL_FOR_ADMIN','cat');
  define('SEFU_PREFIX_CAT_URL','catalog');
  /**
  * Класс CreateSEFU (SEFU - Search Engine Frendly Urls) предназначен для
  * формирования SEF-урлов
  *
  * @author Администратор
  */
  class CreateSEFU {
    protected $help;

    public function  __construct() {
      $this->help = new createSEFU_DB();
    }
    /**
    * Метод applySEFU сохраняет все урлы сайта в формате SEFU
    */
    public function applySEFU(){
      $this->applySEFUCatalogue();
      $this->applySEFUCatalogueBrand();
    }
   

    /**
    * Метод applySEFUCatalogue формирует урлы каталога в формате SEFU.
    * Надо его отрефакторить.
    */
    public function applySEFUCatalogue(){
      $cats = $this->help->getCats(); // достаем ID всех каталогов
      if(!empty($cats)){
        foreach ($cats as $catID){
          $this->_applySEFUCatalogue($catID);
        }
      }
    }
    
    public function _applySEFUCatalogue($catID){
        $siteURL = "/".SEFU_PREFIX_CAT_URL_FOR_ADMIN.'/'.$catID.'/'; // получаем REALCATNAME

        $sefURL = $this->getSEFUCat($catID);  // формируем ЧПУ-урл

        $idSefSiteRelation = $this->help->getIdSefSite($siteURL);

        if($idSefSiteRelation){ // если для узла каталога уже есть ЧПУ
          $sefSiteRelationInfo = $this->help->getSEF_SiteRelationInfo($idSefSiteRelation);
          if($sefURL != $sefSiteRelationInfo['SEF_URL']){
            // если уже существующий ЧПУ для узла каталога НЕ РАВЕН полученному,
            // то существующий записываем в старые, а полученный - на место существующего
            $this->help->addOldURL($idSefSiteRelation,$sefSiteRelationInfo['SEF_URL']);
            $this->help->updateSEF_SiteRelation($idSefSiteRelation,$sefURL);
//            if($this->help->deleteOldNotNeedUrl($sefURL)){
//              continue;
//            }
          }
        } else {
            $idSefSiteRelation = $this->help->saveSiteSEFRelation($siteURL,$sefURL);
            $this->help->addOldURL($idSefSiteRelation, $siteURL);
        }
    }

    public function applySEFUCatalogueBrand(){
      $cats = $this->help->getCats(); // достаем ID всех каталогов
      if(!empty($cats)){
        foreach ($cats as $catID) {
          $this->_applySEFUCatalogueBrand($catID);
        }
      }
    }
    
    public function _applySEFUCatalogueBrand($catID){
      $brands = $this->help->getCatsBrands($catID); // достаем ID всех каталогов
      if(!empty($brands)){
        foreach ($brands as $brandID){                
          /******** формируем SEF ********/          

          $siteURL = "/".SEFU_PREFIX_CAT_URL_FOR_ADMIN.'/'.$catID.'/brand/'.$brandID.'/'; // получаем REALCATNAME

          $sefURL = $this->getSEFUBrand($catID, $brandID);  // формируем ЧПУ-урл                    
          $idSefSiteRelation = $this->help->getIdSefSite($siteURL);
          
          if($idSefSiteRelation){ // если для узла каталога уже есть ЧПУ
            $sefSiteRelationInfo = $this->help->getSEF_SiteRelationInfo($idSefSiteRelation);                    
            if($sefURL != $sefSiteRelationInfo['SEF_URL']){  
              // если уже существующий ЧПУ для узла каталога НЕ РАВЕН полученному,
              // то существующий записываем в старые, а полученный - на место существующего
              $this->help->addOldURL($idSefSiteRelation,$sefSiteRelationInfo['SEF_URL']);
              $this->help->updateSEF_SiteRelation($idSefSiteRelation,$sefURL);
            }
          }
          else
            $idSefSiteRelation = $this->help->saveSiteSEFRelation($siteURL,$sefURL); // сохраняем соответствие урла сайта -- ЧПУ-урлу



//          $oldURL = $siteURL;
//          $this->help->addOldURL($idSefSiteRelation, $oldURL);
        }
      }
    }

    /**
    * Возвращает ЧПУ-урл для каталога $catID
    * @param int $catID
    * @return string
    */
    public function getSEFUCat($catID){
      return trim($this->help->getRealURLCat($catID));
    }
    
    public function getSEFUBrand($catID, $brandID){      
      $catRealCat = trim($this->help->getRealURLCat($catID));
      $brand_name = trim($this->help->getBrandName($brandID));
      
      $translit = new Translit();
      $brand_name = $translit->getLatin($brand_name); // получаем транслитерированный заголовок
      unset($translit);
      
      return $catRealCat.$brand_name.'/';
    }

    /**
    * Возвращает урл сайта, соответствующий ЧПУ-урлу.
    * @param string $sefURL ЧПУ-урл
    * @return string урл сайта
    */
    public function getSiteURLbySEFU($sefURL){
      return $this->help->getSiteURLbySEFU($sefURL);
    }

    /**
    * Возвращает ЧПУ-урл, соответствующий старому урлу.
    * @param string $oldURL старый урл
    * @return string ЧПУ-урл
    */
    public function getSefURLbyOldURL($oldURL){
      return $this->help->getSefURLbyOldURL($oldURL);
    }
  }
?>