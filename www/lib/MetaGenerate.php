<?php

class MetaGenerate{
  
  /**
  * Рабочая модель БД
  * 
  * @var MetaGenerateModel
  */
  private $_model;

  /**
  * put your comment there...
  * 
  * @param iMetaGenerateModel $model
  */
  public function  __construct(iMetaGenerateModel $model) {
    $this->_model = $model;
  }
  
  /**
  * Генерация мета-данных для всего каталога
  * 
  */
  public function metaCatalogue(){
    $result = $this->_model->getCatalogsId();    
    if(!empty($result)){
      foreach($result as $id){
        $this->metaCatalogueById($id);
      }
    }
  }
  
  /**
  * Генерация мета-данных для конкретного каталога
  * 
  * @param int $id
  */
  public function metaCatalogueById($id){
    $data = $this->_model->getCurrentCatalog($id);
    
    if(empty($data['TITLE']) && $data['PARENT_ID'] > 0) 
      $this->generateCatalogueTitle($id, $data['NAME']);
      
    if(empty($data['KEYWORD_META']) && $data['PARENT_ID'] > 0) 
      $this->generateCatalogueKeyword($id, $data['NAME']);
      
    if(empty($data['DESC_META']) && $data['PARENT_ID'] > 0) 
      $this->generateCatalogueDescription($id, $data['NAME']);

  }
  
  /**
  * Генерация HTML Title рубрики каталога
  * 
  * @param int $id
  * @param string $name
  */
  private function generateCatalogueTitle($id, $name){
    $meta_title = $this->_model->getSettingValue('title_catalog_level_two');
    
    $result = $this->_model->getBrands($id);
    $brands = array();
    if (!empty($result)) {
      foreach ($result as $view) {
        $brands[] = $view['NAME'];
      }
    }
    
    $replace_title = array('##name##' => $name
                          ,'##brands##' => implode(', ', $brands)
             );
              
    $title = strtr($meta_title, $replace_title);
    
    $this->_model->updateCatalogue(array('TITLE'=>$title),$id);
  }
  
  /**
  * Генерация HTML Keywords рубрики каталога
  * 
  * @param int $id
  * @param string $name
  */
  private function generateCatalogueKeyword($id, $name){
    
  }
  
  /**
  * Генерация HTML Description рубрики каталога
  * 
  * @param int $id
  * @param string $name
  */
  private function generateCatalogueDescription($id, $name){
    $meta_description = $this->_model->getSettingValue('description_catalog_level_two');
    
    $replace_desc = array('##name##' => $name
             );
              
    $desc_meta = strtr($meta_description, $replace_desc);
    
    $this->_model->updateCatalogue(array('DESC_META'=>$desc_meta),$id);
  }
  
  /**
  * Генерация мета-данных для всех товаров
  * 
  */
  public function metaItems(){
    $result = $this->_model->getItemsId();    
    if(!empty($result)){
      foreach($result as $id){
        $this->metaItemById($id);
      }
    }
  }
  
  
  /**
  * Генерация мета-данных для конкретного товара
  * 
  * @param int $id
  */
  public function metaItemById($id){
    $data = $this->_model->getCurrentItem($id);

    $item_name = '';
    if (!empty($data['TYPENAME']))
        $item_name.= ' ' . $data['TYPENAME'];
    if (!empty($data['BRAND_NAME']))
        $item_name.= ' ' . $data['BRAND_NAME'];
    if (!empty($data['NAME']))
        $item_name.= ' ' . $data['NAME'];

    $item_name = trim($item_name);    
    
    if(empty($data['TITLE'])) 
      $this->generateItemTitle($id, $item_name);
      
    if(empty($data['KEYWORD_META'])) 
      $this->generateItemKeyword($id, $item_name);
      
    if(empty($data['DESC_META'])) 
      $this->generateItemDescription($id, $item_name);
  }
  
  /**
  * Генерация HTML Title товарв
  * 
  * @param int $id
  * @param string $name
  */
  private function generateItemTitle($id, $name){
    $meta_title = $this->_model->getSettingValue('title_item');
    
    
    $replace_title = array('##name##' => $name
                     );
                      
    $title = strtr($meta_title, $replace_title);
    
    $this->_model->updateItem(array('TITLE'=>$title),$id);
  }
  
  /**
  * Генерация HTML Keywords товарв
  * 
  * @param int $id
  * @param string $name
  */
  private function generateItemKeyword($id, $name){
    
  }
  
  /**
  * Генерация HTML Description товарв
  * 
  * @param int $id
  * @param string $name
  */
  private function generateItemDescription($id, $name){
    $meta_description = $this->_model->getSettingValue('description_item');
    
    $replace_desc = array('##name##' => $name
                     );
                      
    $desc_meta = strtr($meta_description, $replace_desc);
    
    $this->_model->updateItem(array('DESC_META'=>$desc_meta),$id);
  }
}