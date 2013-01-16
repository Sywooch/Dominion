<?php
  class generateDescription{
    private $Items;
    
    public function __construct(){
      Zend_Loader::loadClass('models_Item');
      
      $this->Items = new models_Item();
    }
    
    public function run(){
      $items_id =  $this->Items->getAllItemId();
      if(!empty($items_id)){
        foreach($items_id as $item){
          $attributes = array();
          $attribute_list = $this->Items->getAttributesDescription($item['CATALOGUE_ID']); 
          if(!empty($attribute_list)){
            $attributes = $this->Items->getItemAttributes($attribute_list, $item['ITEM_ID']);
            
            if(!empty($attributes)){
              $description = '';
              foreach($attributes as $key=>$attr){                
                if($key > 0) $description.= '<i class="dvdr">/</i> ';
                switch($attr['in_podbor']){    
                  case 1:
                    $description.= $attr['name'];
                  break;
                  
                  case 2:
                    $description.= $attr['value'].' '.$attr['unit_name'];
                  break;
                  
                  case 3:
                    $description.= $attr['name'].': <b>'.$attr['value'].' '.$attr['unit_name'].'</b>';
                  break;
                }
              }
              
              $this->Items->updateItem($item['ITEM_ID'], $description);
            }
          }        
        }
      }
      
    }
        
  }
?>