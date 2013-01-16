<?php
class Helpers_ShortAttributs extends App_Controller_Helper_HelperAbstract{
  
  private $cart;
  private $compare;
  
  public function getShortAttributs($item_id, $catalogue_id){
    $Attributs = new models_Attributs();
    
    $item_attr_short = array();
    $shorts = $this->work_model->getAttributes($catalogue_id,'ATTR_CATALOG_VIS');
    $item_attr_short = $this->work_model->getItemAttributes($shorts,$item_id,$catalogue_id);      
    
    $childs = $this->initChilds($catalogue_id);
    $similar_items_id = $this->work_model->getCatalogItemsID($childs, $item_id);
    
    $params = $this->initShortAttributsValues($item_attr_short);            
    $tmp = $this->work_model->initTempShortAttrTable($params, $similar_items_id);
    
    $session = new Zend_Session_Namespace('short_atr');
    $session_short_atr = $session->short_atr;
    $short_atr_unsel = $session->short_atr_unsel;
    
    if(!empty($item_attr_short)){
      $this->domXml->set_tag('//data', true);
      foreach($item_attr_short as $k=>$attribute){
         if(in_array($attribute['attribut_id'], $tmp)){          
           $sel = 0;             
           $lock = 0;             
           
           if(isset($session_short_atr) && is_array($session_short_atr) && array_key_exists($attribute['attribut_id'], $session_short_atr)) $sel = 1;
           if(isset($short_atr_unsel) && is_array($short_atr_unsel) && array_key_exists($attribute['attribut_id'], $short_atr_unsel)) $lock = 1;
           
           $this->domXml->create_element('attr_shorts','',2);
           $this->domXml->set_attribute(array('attribut_id'  => $attribute['attribut_id']
                                            , 'is_rangeable' => $attribute['is_rangeable']
                                            , 'sel' => $sel
                                            , 'lock' => $lock));
                                            
           if($attribute['type']==5 || $attribute['type']==6){
             if(empty($attribute['value'])){
               $val = $attribute['val'];
               $val_view = $attribute['val'];
             }
             else{
               $val = $attribute['value'];
               $val_view = $attribute['value'];
             }
           }
           elseif($attribute['type']==3 || $attribute['type']==4){
             $val = $attribute['val'];
             $val_view = $attribute['value'];               
           }
           else{
             $val = $attribute['value'];
             $val_view = $attribute['value'];
           }
                              
           $this->domXml->create_element('name',$attribute['name']);
           $this->domXml->create_element('type',$attribute['type']);
           $this->domXml->create_element('unit_name',$attribute['unit_name']);
           $this->domXml->create_element('value_view',$val_view);
           $this->domXml->create_element('value',$val);
           
           if($attribute['is_rangeable']==1){
             $range_name = $Attributs->getRangeName($attribute['val']);
             $this->domXml->create_element('range_name',$range_name);
           }

           $this->domXml->go_to_parent();
         }
      }          
    }
  }
  
  public function getSimilarItem($item_id, $attr){
    $similar_items = array();
    
    $catalogue_id = $this->work_model->getItemCatalog($item_id);

//    $this->work_model->getSimilarTempTable($catalogue_id);
    
//    $table = $this->work_model->initTempTable($attr);
    
    $item_cash_arr = array();
    
    $item_cash = $this->work_model->getSearchItemCash($item_id);
    $temp_item_cash = array_map('trim', explode(' ', $item_cash));
    if(!empty($attr)){
      foreach($attr as $agid=>$atid){
        $val = 'a'.$agid.'v'.$atid;
        $item_cash_arr[] = $val;
        if($key = array_search($val, $temp_item_cash)){
          unset($temp_item_cash[$key]);
        }
      }
    }
    
    $items = $this->work_model->getSearchItems($item_id, $catalogue_id, $item_cash_arr, $temp_item_cash);
    
    if(!empty($items)){          
      $params['items_id'] = $items;
      $params['start'] = 0;
      $params['per_page'] = $this->params['per_page'];
      
      $params['order_by'] = 'I.PRICE';
      $params['asc'] = 'asc';
      $similar_items = $this->work_model->getCatItems($params);
    }


    if(!empty($similar_items)){
      $this->domXml->set_tag('//page', true);
      $this->viewSimilarItems($similar_items);
    }
        
    $_path = $this->params['view']->getScriptPath('ajaxload-item-tovar.xsl');
    if(is_file($_path)){
      return $this->simpleTransform($_path, $this->domXml->getXML());
    }
    
    return '';    
  }
  
  public function shortAttribMode($item_id, $attr){    
    $catalogue_id = $this->work_model->getItemCatalog($item_id);    
      
    $attr_str = array();
    $attr_params = array();

    $shorts = $this->work_model->getAttributes($catalogue_id,'ATTR_CATALOG_VIS');
    $item_attr_short = $this->work_model->getItemAttributes($shorts,$item_id,$catalogue_id);
    $item_attr_short = $this->initShortAttributs($item_attr_short);
    if(!empty($attr)){
      foreach($attr as $atgid=>$atid){
        $attr_params[] = 'a'.$atgid.'v'.$atid;
      }
    }

    $similar_items_id = $this->work_model->getSearchItems($item_id, $catalogue_id, $attr_params, null);        
    
    $sm_item_cash = array();
    if(!empty($similar_items_id)){
      foreach($similar_items_id as $smid){
        
        $item_cash = $this->work_model->getSearchItemCash($smid);
        $sm_item_cash = array_merge($sm_item_cash, array_map('trim', explode(' ', $item_cash)));
      }  
      
      $sm_item_cash = array_unique($sm_item_cash);
      
      foreach($item_attr_short as $key=>$val){
        $_attr = 'a'.$key.'v'.$val;
        $attr_str[$key]['id'] = 'attr'.$key;
        $attr_str[$key]['val'] = 0;
        
        if(in_array($_attr, $sm_item_cash)){
          $attr_str[$key]['val'] = 1;
        }
      }
    }
    else{
      foreach($item_attr_short as $key=>$attr){
        $attr_str[$key]['id'] = 'attr'.$attr['attribut_id'];
        $attr_str[$key]['val'] = 1;
      }         
    }
    
    return $attr_str; 
  }
  
  private function initChilds($catalogue_id){
    $Catalogue = new models_Catalogue();
    $parent = $catalogue_id;
    while($parent > 0){
      $cat = $Catalogue->getParents($parent);
      $parent = $cat['PARENT_ID'];
      if($parent == 0) break;
    }
    
    $childs = array();
    $childs = $Catalogue->getChildren($cat['CATALOGUE_ID']);
    $childs[count($childs)] = $cat['CATALOGUE_ID'];
    
    return $childs;
  }
  
  private function initShortAttributs($item_attr_short){
    $params = array();
    if(!empty($item_attr_short)){
      foreach($item_attr_short as $k=>$attribute){             
        $params[$attribute['attribut_id']] = $attribute['val'];
      }
    }

    return $params;
  }
  
  private function initShortAttributsValues($item_attr_short){
    $params = array();
    if(!empty($item_attr_short)){
      foreach($item_attr_short as $k=>$attribute){             
        if($attribute['is_rangeable']==1){
          $params[$attribute['attribut_id']] = $attribute['val'];
        }
        else{
          $params[$attribute['attribut_id']] = $attribute['value'];
        }
      }
    }

    return $params;
  }
  
  private function viewSimilarItems($similar_items){
    $session = new Zend_Session_Namespace('cart');
    $this->cart = $session->item;
    
    $session = new Zend_Session_Namespace('compare');
    $this->compare = $session->compare;
        
    $curr_info = $this->work_model->getCurrencyInfo($this->params['currency']);
    
    foreach($similar_items as $item){      
      $this->doItemXmlNode($item, $curr_info, $all_info = false, $node_name ='similar_items');
    }
  }
  
  /**
  * Формирование XML карточки товара
  * 
  * @param array $item
  * @param array $curr_info
  * @param boolean $all_info
  */
  private function doItemXmlNode($item, $curr_info, $all_info = false, $node_name ='item'){
    $item['sh_disc_img_small'] = '';
    $item['sh_disc_img_big'] = '';
    $item['has_discount'] = 0;
    
    list($new_price, $new_price1) = $this->work_model->recountPrice($item['PRICE'],$item['PRICE1'],$item['CURRENCY_ID'],$this->params['currency'], $curr_info['PRICE']);

    if($this->params['currency'] > 1){
      $item['iprice'] = round($new_price,1);
      $item['iprice1'] = round($new_price1,1);
    }
    else{
      $item['iprice'] = round($new_price);
      $item['iprice1'] = round($new_price1);
    }                       
    
    $params['currency'] = $this->params['currency'];            
    $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');
    $ct_helper = $helperLoader->loadHelper('Cart', $params);
    $ct_helper->setModel($this->work_model);
    $item = $ct_helper->recountPrice($item);

    if(isset($this->cart[$item['ITEM_ID']]) && !empty($this->cart[$item['ITEM_ID']])){
      $in_cart = 1; 
      $in_cart_count = $this->cart[$item['ITEM_ID']]['count']; 
    }
    else{
      $in_cart = 0;
      $in_cart_count = 1;
    } 
    
    if(isset($this->compare[$item['CATALOGUE_ID']][$item['ITEM_ID']]) && 
       !empty($this->compare[$item['CATALOGUE_ID']][$item['ITEM_ID']])){
      $in_compare = 1; 
    }
    else{
      $in_compare = 0;
    }
                  
    $node_attr = array('item_id'  => $item['ITEM_ID']
                      ,'price'   =>  $item['iprice']
                      ,'price1' =>  $item['iprice1']
                      ,'real_price'   =>  $item['PRICE']
                      ,'real_price1' =>  $item['PRICE1']
                      ,'in_cart' =>  $in_cart
                      ,'in_compare' =>  $in_compare
                      ,'in_cart_count' =>  $in_cart_count
                      ,'catalogue_id' =>  $item['CATALOGUE_ID']
                      ,'has_discount' =>  $item['has_discount']
                      ,'active' => $item['STATUS']);


    $this->domXml->create_element($node_name,'',2);
    $this->domXml->set_attribute($node_attr);

    $href = $this->lang.$item['CATALOGUE_REALCATNAME'].$item['ITEM_ID'].'-'.$item['CATNAME'].'/';
    $href_goods_category = $item['CATALOGUE_REALCATNAME'];

    $this->domXml->create_element('name',$item['NAME']);    
    $this->domXml->create_element('brand_name',$item['BRAND_NAME']);    
    $this->domXml->create_element('short_description', nl2br($item['DESCRIPTION']));
    $this->domXml->create_element('sname',$curr_info['SNAME']);
    $this->domXml->create_element('nat_sname',$item['SNAME']);

    $this->domXml->create_element('href',$href);
    $this->domXml->create_element('href_goods_category',$href_goods_category);
    
    if(!empty($item['sh_disc_img_small']) && strchr($item['sh_disc_img_small'],"#")){
      $tmp=explode('#',$item['sh_disc_img_small']);
      $this->domXml->create_element('sh_disc_img_small','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                   'w'    => $tmp[1],
                                   'h'    => $tmp[2]
                                   )
                                  );
      $this->domXml->go_to_parent();
    }
                            
    if(!empty($item['sh_disc_img_big']) && strchr($item['sh_disc_img_big'],"#")){
      $tmp=explode('#',$item['sh_disc_img_big']);
      $this->domXml->create_element('sh_disc_img_big','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                   'w'    => $tmp[1],
                                   'h'    => $tmp[2]
                                   )
                                  );
      $this->domXml->go_to_parent();
    }

    $this->itemImages($item);

    $this->domXml->go_to_parent();
  }
          
  /**
  * Формирование XMK фото товара
  * 
  * @param array $item
  */

  private function itemImages($item){
    if(!empty($item['IMAGE1']) && strchr($item['IMAGE1'],"#")){
      $tmp=explode('#',$item['IMAGE1']);
      $this->domXml->create_element('image_small','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                   'w'    => $tmp[1],
                                   'h'    => $tmp[2]
                                   )
                                  );
      $this->domXml->go_to_parent();
    }
    
    if(!empty($item['IMAGE2']) && strchr($item['IMAGE2'],"#")){
      $tmp=explode('#',$item['IMAGE2']);
      $this->domXml->create_element('image_middle','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                   'w'    => $tmp[1],
                                   'h'    => $tmp[2]
                                   )
                                  );
      $this->domXml->go_to_parent();
    }
    
    if(!empty($item['IMAGE3']) && strchr($item['IMAGE1'],"#")){
      $tmp=explode('#',$item['IMAGE3']);
      $this->domXml->create_element('image_big','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                   'w'    => $tmp[1],
                                   'h'    => $tmp[2]
                                   )
                                  );
      $this->domXml->go_to_parent();
    }
    
    if(!empty($item['DISCOUNTS_IMAGE']) && strchr($item['DISCOUNTS_IMAGE'],"#")){
      $tmp=explode('#',$item['DISCOUNTS_IMAGE']);
      $this->domXml->create_element('discount_image','',2);
      $this->domXml->set_attribute(array('src'  => $tmp[0],
                                         'w'    => $tmp[1],
                                         'h'    => $tmp[2]
                                         ));
      $this->domXml->go_to_parent();
    }
  }        
  
  private function simpleTransform($xslfile, $xml){
    
    $xslt = new xsltProcessor;
        
    $xsl = new DOMDocument;
    $xsl->resolveExternals = TRUE;
    $xsl->substituteEntities = TRUE;

    $xsl->load($xslfile);
    $xslt->importStyleSheet($xsl);
                
    $pattern = "/<page>.*<\/page>/Uis";
    if(preg_match($pattern, $xml, $matches))
      $xml = $matches[0];
    else
      $xml='<data>proba pera</data>';
    

    $xml_object = new DOMDocument;
    $xml_object->resolveExternals = TRUE;
    $xml_object->substituteEntities = TRUE;
    $xml_object->loadXML($xml);

    return $xslt->transformToXML($xml_object);
  }
}