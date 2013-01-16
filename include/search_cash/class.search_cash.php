<?php

  class search_cash{
    
    private $_db;
    private $_search_cash;
    private $_prev_attr = 0;
    
    function __construct() {
      Zend_Loader::loadClass('models_SearchCash');
      
      $this->_db = new models_SearchCash();
    }
    
    /**
    * Стар работы скрипта
    * 
    */    
    public function buildCash(){
      $this->resetItem();
      $this->resetSearchCash();
      
      $item_data = true;
      
      while($item_data){
        $item_data = $this->_db->getItem();
        if(!empty($item_data)){
          $this->processingItem($item_data);  
        }
      }      
    }
    
    /**
    * Обнуление товаров в исходное состояние
    * 
    */    
    public function resetItem(){
      $this->_db->resetItem();
    }
    
    /**
    * Обнуление таблицы кеша
    * 
    */    
    public function resetSearchCash(){
      $this->_db->resetSearchCash();
    }
    
    /**
    * Создание кеша товара
    * 
    */
    private function processingItem($item_data){      
      
      $this->_search_cash = array();
      $attributs = $this->_db->getVisAttr($item_data['CATALOGUE_ID']);
      
      if(!empty($attributs)){
        foreach($attributs as $attr){
          
          switch($attr['TYPE']){
            case 0:
            case 3:
            case 4:
            case 5:
            case 6:
              $this->processingItemZero($item_data, $attr);
            break;
            
            case 1:
              $this->processingItemZero($item_data, $attr);
            break;
            
//            case 2:
//              $this->processingItemZero($item_data, $attr);
//            break;
            
//            case 7:
//              $this->processingItemZero($item_data, $attr);
//            break;
          }
          
          $this->_prev_attr = $attr['ATTRIBUT_ID'];
        }
      }
      
      if(!empty($this->_search_cash)){
        foreach($this->_search_cash as $search_cash){
          $insert_data['ITEMS_COUNT'] = count($search_cash['search_items']);
          if($insert_data['ITEMS_COUNT'] > 0){
            $insert_data['SEARCH_CASH'] = md5($item_data['CATALOGUE_ID'].$search_cash['search_cash']);
            $insert_data['SEARCH_NAME'] = $item_data['CATALOGUE_ID'].'-'.$search_cash['search_cash'];
            $insert_data['ITEMS'] = serialize($search_cash['search_items']);
            
            $this->_db->insert_data('SEARCH_CASH', $insert_data);
            $this->_db->update_item($search_cash['search_items']);  
          }
          
        }          
      }
      
      $this->_db->update_item(array($item_data['ITEM_ID']));  
    }
    
    /**
    * Обработка атрибутов таблицы ITEM0
    * 
    */
    private function processingItemZero($item_data, $attribut_data){
      $params['attr_type'] = $attribut_data['TYPE'];
      $params['item_id'] = $item_data['ITEM_ID'];
      $params['attr_id'] = $attribut_data['ATTRIBUT_ID'];
      $params['catalogue_id'] = $item_data['CATALOGUE_ID'];
      
      $value = $this->_db->getItemAttrValue($params);
      
      if(!empty($value)){
        $params['value'] = $value;
        
        if(!empty($this->_prev_attr) && !empty($this->_search_cash[$this->_prev_attr])){
          $prev_attr = $this->_search_cash[$this->_prev_attr]['search_cash'];
          $search_items = $this->_search_cash[$this->_prev_attr]['search_items'];
                  
          $uniq_items = array_unique($this->_db->getSimilarAttrItems($params, $search_items));
          
          $this->_search_cash[$attribut_data['ATTRIBUT_ID']]['search_cash'] = $prev_attr.'a'.$attribut_data['ATTRIBUT_ID'].'v'.$value;
          $this->_search_cash[$attribut_data['ATTRIBUT_ID']]['search_items'] = $uniq_items;
        }
        else{
          $this->_search_cash[$attribut_data['ATTRIBUT_ID']]['search_cash'] = 'a'.$attribut_data['ATTRIBUT_ID'].'v'.$value;
          $this->_search_cash[$attribut_data['ATTRIBUT_ID']]['search_items'] = $this->_db->getSimilarAttrItems($params);
        }  
      }
      
        
    }
    
  }
?>