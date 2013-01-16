<?php
  class models_PriceRecount{
    
    public function priceRecount($params){
      $Registration = new models_Registration();
      
      $_price_result['new_price'] = 0;
      $_price_result['real_price'] = 0;
      $_price_result['sh_disc_img_small'] = '';
      $_price_result['sh_disc_img_big'] = '';
      
      $shopuser_discounts_id = $Registration->getShopuserDiscountsId($params['user_id']);
      if(!empty($shopuser_discounts_id)){
        list($new_price, $real_price) = $this->getDiscount($shopuser_discounts_id, $params);  
        
        $_price_result['new_price'] = $new_price;  
        $_price_result['real_price'] = $real_price;  
        
        list($_price_result['sh_disc_img_small'], $_price_result['sh_disc_img_big']) = $this->getUserDiscountImages($shopuser_discounts_id, $new_price);
      }
      
      return $_price_result;
    }
    
    public function getDiscount($shopuser_discounts_id, $params){
      $result_price  = 0;
      $Item = new models_Item();
      
      $curr_info = $Item->getCurrencyInfo($params['real_currency_id']);
      
      $discount = $Item->getUserDiscountRang($shopuser_discounts_id, $params['price']);
      if(!empty($discount)){
        if(strpos($discount, '%')!==false){
          $discount = trim(str_replace('%', '', $discount));
          $discount = str_replace(',', '.', $discount);
          $result_price = round(($params['price'] * 100) / (100 + $discount));
        }
        else{
          $result_price = $params['price'] - $discount;
        }
        
        list($real_price, $new_price1) = $Item->recountPrice($result_price,0,$params['currency_id'],$params['real_currency_id'], $curr_info['PRICE']);
      }
      
      return array($result_price, $real_price);
    }
    
    /**
    * Узнать какие иконки для скидки покупателя
    * 
    * @param int $shopuser_discounts_id
    * @param int $price
    * @return array
    */
    
    private function getUserDiscountImages($shopuser_discounts_id, $price){      
      $sh_disc_img_small = '';
      $sh_disc_img_big = '';
      
      if(!empty($price)){
        $Item = new models_Item();
        
        $result = $Item->getUserDiscountImages($shopuser_discounts_id);
        if(!empty($result)){
          $sh_disc_img_small = $result['IMAGE1'];
          $sh_disc_img_big = $result['IMAGE2'];
        }        
      }      
      
      return array($sh_disc_img_small, $sh_disc_img_big);
    }
    
  }
?>