<?php

  class saler_data implements iPlaceholders{
    
    const PLACEHOLDER = 'tovar_check_forma';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){
      $result = array();
      $value = $this->_model->getSettings(self::PLACEHOLDER);
      
      $value = nl2br($value);
      $value = explode('<br />', $value);
      if(!empty($value)){
        foreach($value as $val){
          $val = trim($val);
          if(empty($val)) continue;
          
          $result[] = $val;
        }
      }
      
      return $result;
    }
  }
  
  class buyer_name implements iPlaceholders{
    
    const PLACEHOLDER = 'tovar_check_pokupatel';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){
      $value = $this->_model->getBuyerName($params['zakaz_id']);
      
      if(empty($value)){
        $value = $this->_model->getSettings(self::PLACEHOLDER);  
      }
      
      $value = trim($value);
      
      return array($value);
    }
  }
  
  class title_row implements iPlaceholders{
    
    const PLACEHOLDER = 'tovar_check_title_row';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    protected $monthes = array('01'=>'января',
                                '02'=>'февраля',
                                '03'=>'марта',
                                '04'=>'апреля',
                                '05'=>'мая',
                                '06'=>'июня',
                                '07'=>'июля',
                                '08'=>'августа',
                                '09'=>'сентября',
                                '10'=>'октября',
                                '11'=>'ноября',
                                '12'=>'декабря',);
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){      
      $result = array();
      $number = '';
      $date = '';
      
      $value = $this->_model->getSettings(self::PLACEHOLDER);
      
      $zakaz_data = $this->_model->getZakazInfo($params['zakaz_id']);
      
      if(!empty($zakaz_data)){
        $number = $zakaz_data['day'].'/'.$zakaz_data['month'].'-'.$zakaz_data['year'].'-'.$params['zakaz_id'];
        $date = $zakaz_data['day'].' '.$this->monthes[$zakaz_data['month']].' '.$zakaz_data['year'];  
      }
      
      $value = nl2br($value);
      $value = explode('<br />', $value);
      if(!empty($value)){
        foreach($value as $val){
          $val = trim($val);
          if(empty($val)) continue;
          
          $replace = array('##number##' => $number
                          ,'##date##' => $date
                          );
                          
          $val = strtr($val, $replace);
          
          $result[] = $val;
        }
      }
      
      return $result;
    }
  }
  
  class razhod_title_row implements iPlaceholders{
    
    const PLACEHOLDER = 'tovar_check_razhod_title_row';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    protected $monthes = array('01'=>'января',
                                '02'=>'февраля',
                                '03'=>'марта',
                                '04'=>'апреля',
                                '05'=>'мая',
                                '06'=>'июня',
                                '07'=>'июля',
                                '08'=>'августа',
                                '09'=>'сентября',
                                '10'=>'октября',
                                '11'=>'ноября',
                                '12'=>'декабря',);
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){      
      $result = array();
      $number = '';
      $date = '';
      
      $value = $this->_model->getSettings(self::PLACEHOLDER);
      
      $zakaz_data = $this->_model->getZakazInfo($params['zakaz_id']);
      
      if(!empty($zakaz_data)){
        $number = $zakaz_data['day'].'/'.$zakaz_data['month'].'-'.$zakaz_data['year'].'-'.$params['zakaz_id'];
        $date = $zakaz_data['day'].' '.$this->monthes[$zakaz_data['month']].' '.$zakaz_data['year'];  
      }
      
      $value = nl2br($value);
      $value = explode('<br />', $value);
      if(!empty($value)){
        foreach($value as $val){
          $val = trim($val);
          if(empty($val)) continue;
          
          $replace = array('##number##' => $number
                          ,'##date##' => $date
                          );
                          
          $val = strtr($val, $replace);
          
          $result[] = $val;
        }
      }
      
      return $result;
    }
  }
  
  class client_data implements iPlaceholders{
    
    const PLACEHOLDER = '';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){      
      $result = array();
      $number = '';
      $date = '';
      
      $zakaz_data = $this->_model->getZakazInfo($params['zakaz_id']);
      
      if(!empty($zakaz_data)){
        $result[] = $zakaz_data['NAME'];
        $result[] = $zakaz_data['TELMOB'];
        $result[] = $zakaz_data['ADDRESS'];  
      }
      
      return $result;
    }
  }
  
  class defaultPlaceholder implements iPlaceholders{
    
    const PLACEHOLDER = 'tovar_';
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    private $_model;
    
    public function __construct($model){
      $this->_model = $model;      
    }
    
    public function getPlaceholderData($params = array()){
      $value = $this->_model->getSettings(self::PLACEHOLDER.$params['placeholder']);  
      
      $value = trim($value);
      
      return array($value);
    }
  }

?>