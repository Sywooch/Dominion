<?php
interface iMetaGenerateModel{
  /**
  * Получить ID всех рубрик каталога товаров
  * 
  */
  public function getCatalogsId();
  
  /**
  * Получить конкретный каталог товаров
  * 
  * @param int $id
  */
  public function  getCurrentCatalog($id);
  
  /**
  * Получить ID всех товаров
  * 
  */
  public function  getItemsId();
  
  /**
  * Получить конкретный товар
  * 
  * @param int $id
  */
  public function  getCurrentItem($id);
  
  /**
  * put your comment there...
  * 
  * @param string $where
  */
  public function getSettingValue($where);
  
  public function getBrands($catid = 0);
  
  public function updateCatalogue($data, $id);
  
  public function updateItem($data, $id);
}