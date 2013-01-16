<?php
class Helpers_GoodsGroups extends App_Controller_Helper_HelperAbstract{
      
  public function getGoodsGroups($indent, $delim){
    $goods_groups_id = $this->work_model->getGoodsGroupsId($indent);
    if($goods_groups_id){
      $goods_groups_info = $this->work_model->getGoodsGroupsInfo($goods_groups_id);
      $this->domXml->create_element($indent,'',2);
      $this->domXml->create_element('name',$goods_groups_info['name']);
      
      $result  = $this->work_model->getGoodsGroupsItems($goods_groups_id);      
      
      if($result){
        $curr_info = $this->params['Item']->getCurrencyInfo($this->params['currency']);
        
        foreach($result as $key=>$item){
          list($new_price, $new_price1) = $this->params['Item']->recountPrice($item['price'],$item['price1'],$item['currency_id'],$this->params['currency'], $curr_info['price']);

          if($this->params['currency'] > 1){
            $item['iprice'] = round($new_price,1);
            $item['iprice1'] = round($new_price1,1);
          }
          else{
            $item['iprice'] = round($new_price);
            $item['iprice1'] = round($new_price1);
          }
          
          if($delim > 0){
            if($key % $delim == 0 && $key > 1){
              $this->domXml->go_to_parent();
            }
            if($key % $delim == 0){
              $this->domXml->create_element('items','',2);
            }  
          }
          
          
          $this->domXml->create_element('item','',2);
          $this->domXml->set_attribute(array('item_id'  => $item['id']
                                            ,'price'   =>  $item['iprice']
                                            ,'price1' =>  $item['iprice1']
                                            ));

          $href = $this->lang.'/item/'.$item['id'].'-'.$item['file_name'].'.html';

          $this->domXml->create_element('name',$item['name']);
          $this->domXml->create_element('short_description', nl2br($item['short_description']));
          $this->domXml->create_element('sname',$curr_info['sname']);
          $this->domXml->create_element('href',$href);
          
          $this->itemImages($item);
                    
          $this->domXml->go_to_parent();                
        }
      }
      
      $this->domXml->go_to_parent();
    }
  }
  
  /**
  * Формирование XMK фото товара
  * 
  * @param array $item
  */

  private function itemImages($item){
    if(!empty($item['image_name'])){
      $this->domXml->create_element('image_small','',2);
      $this->domXml->set_attribute(array('src'  => $item['image_name'],
      'w'    => $item['small_x'],
      'h'    => $item['small_y']
      )
      );
      $this->domXml->go_to_parent();
    }

    if(!empty($item['image_name1'])){
      $this->domXml->create_element('image_middle','',2);
      $this->domXml->set_attribute(array('src'  => $item['image_name1'],
      'w'    => $item['middle_x'],
      'h'    => $item['middle_y']
      )
      );
      $this->domXml->go_to_parent();
    }

    if(!empty($item['image_name2'])){
      $this->domXml->create_element('image_big','',2);
      $this->domXml->set_attribute(array('src'  => $item['image_name2'],
      'w'    => $item['big_x'],
      'h'    => $item['big_y']
      )
      );
      $this->domXml->go_to_parent();
    }
  }
}
