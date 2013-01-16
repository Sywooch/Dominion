<?php
  abstract class check_rashod{
  /**
    * put your comment there...
    * 
    * @var Report\iConfig
    */
    protected $_config;
    
    protected $_cells;
    
    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    protected $_model;
    
    
    protected $_zakaz_id;
    
    function __construct(iConfig $config, checkModel $model, $zakaz_id){
      $this->_config = $config;      
      $this->_model = $model;      
      $this->_zakaz_id = $zakaz_id;      
      
      $this->_cells = new SplObjectStorage();
    }
    
    /**
    * put your comment there...
    * 
    * @return \SplObjectStorage
    */
    
    public function getExcelData(){
      return $this->_cells;
    }
    
    protected function processCarcasData(){
      foreach($this->_config->getCellsData() as $config){                        
        if($config->isPlaceholder()){          
          $value = $this->getPlaceholderData($config->getValue());
          
          if(!empty($value) && is_array($value)){
            $coord = $config->getCoord();
            
            $pattern = '/^(\D*)(\d*)$/is';
            if(preg_match($pattern, $coord, $out)){
              $col = (string)$out[1];
              $row = (int)$out[2];
              foreach($value as $val){
                $excelCell = new ExcelData();
                
                $excelCell->setCoord($col.$row);
                $excelCell->setValue($val);
                
                if($config->getStyle()){
                  $excelCell->setStyle($config->getStyle());
                }
                
                if($config->getWidth()){
                  $excelCell->setWidth($config->getWidth());
                }
                
                if($config->getHeight()){
                  $excelCell->setHeight($config->getHeight());
                }
                
                $this->attach($excelCell);
                $row++;
              }  
            }            
          }          
        }
        else{
          $excelCell = new ExcelData();
          
          $excelCell->setCoord($config->getCoord());
          $excelCell->setValue($config->getValue());
          if($config->getStyle()){
            $excelCell->setStyle($config->getStyle());
          }
          
          if($config->getImage()){
            $excelCell->setImage($config->getImage());
          }
          
          if($config->getWidth()){
            $excelCell->setWidth($config->getWidth());
          }
          
          if($config->getHeight()){
            $excelCell->setHeight($config->getHeight());
          }
          
          $this->attach($excelCell);
        }
      }      
    }
    
    protected function processTableData($start){
      $result = array();
      $summ = 0;
      
      
      $zakaz_item = $this->_model->getZakazItem($this->_zakaz_id);
      $default_currency = $this->_model->getDefaultCurrency();
      
      if(!empty($zakaz_item)){
        foreach($zakaz_item as $key=>$view){
          $name = $view['TYPENAME'].' '.$view['BRAND_NAME'].' '.$view['NAME']; 
          
          if($default_currency['CURRENCY_ID'] != $view['CURRENCY_ID']){
            $rate = $this->_model->getCurrencyRate($view['CURRENCY_ID']);
            
            $view['PRICE'] = round($view['PRICE'] * $rate);
          }
          /* =============== № =======================*/
          $result[$start+$key]['A']['value'] = $key+1;
          $result[$start+$key]['A']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        ),
                                                        'alignment'=>array(
                                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                        )
                                                      );
                                                              
          $result[$start+$key]['A']['wrap'] = false;
          
          /* =============== Товар =======================*/
          $result[$start+$key]['B']['value'] = $name;
          $result[$start+$key]['B']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        )
                                                      );
          $result[$start+$key]['B']['wrap'] = true;
          
          /* =============== Ед =======================*/
          $result[$start+$key]['C']['value'] = 'шт.';
          $result[$start+$key]['C']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        ),
                                                        'alignment'=>array(
                                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                        )
                                                      );
                                                              
          $result[$start+$key]['C']['wrap'] = false;
          
          /* =============== Количество =======================*/
          $result[$start+$key]['D']['value'] = $view['QUANTITY'];
          $result[$start+$key]['D']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        ),
                                                        'alignment'=>array(
                                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                        )
                                                      );
                                                              
          $result[$start+$key]['D']['wrap'] = false;
          
          /* =============== Цена =======================*/
          $result[$start+$key]['E']['value'] = $view['PRICE'];
          $result[$start+$key]['E']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        ),
                                                        'alignment'=>array(
                                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                        )
                                                      );
                                                              
          $result[$start+$key]['E']['wrap'] = false;
          
          /* =============== Сума =======================*/
          $result[$start+$key]['F']['value'] = round($view['PRICE'] * $view['QUANTITY']);
          $result[$start+$key]['F']['style'] =  array(
                                                      'borders' => array(
                                                        'outline' => array(
                                                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                          'color' => array('argb' => '000'),
                                                          ),
                                                        ),
                                                        'font'=>array(
                                                          'name'=>'Arial Cyr',
                                                          'size'=>'8',
                                                          'bold'=>false
                                                        ),
                                                        'alignment'=>array(
                                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                        )
                                                      );
                                                              
          $result[$start+$key]['F']['wrap'] = false;
          
          $summ+=round($view['PRICE'] * $view['QUANTITY']);
        }
        
      }
      
      if(!empty($result)){        
        foreach($result as $k_row=>$row){
          foreach($row as $k_col=>$cell){
            $excelCell = new ExcelData();
          
            $excelCell->setCoord($k_col.$k_row);
            $excelCell->setValue($cell['value']);
            $excelCell->setStyle($cell['style']);
            $excelCell->setWrap($cell['wrap']);
            
            $this->attach($excelCell);
          }
        }                
        
        $this->underTable($k_row, $summ);
      }
      
    }
    
    protected function underTable($row, $summ){
      $row++;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(4, $row));
      $excelCell->setValue('Вcего на сумму');      
      $this->attach($excelCell);
      
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(5, $row));
      $excelCell->setValue($summ);      
      $this->attach($excelCell);
      
      $row = $row + 2;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(0, $row));
      $excelCell->setValue('Вcего на сумму');      
      $this->attach($excelCell);
      
      $row++;
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(0, $row));
      $excelCell->setValue($this->summ_propis($summ));      
      $this->attach($excelCell);
      
      $row = $row + 2;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(3, $row));
      $excelCell->setValue('Выписал');      
      $this->attach($excelCell);
      
      $row++;
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(4, $row));
      $excelCell->setValue($this->_model->getSettings('tovar_check_vipisal'));
      $this->attach($excelCell);
    }
    
    protected function attach($excelCell){
      $this->_cells->attach($excelCell);
    }
    
    protected function getPlaceholderData($placeholder){
      if(class_exists($placeholder)){
        $objPlh = new $placeholder($this->_model);
        $params['zakaz_id'] = $this->_zakaz_id;
        
        return $objPlh->getPlaceholderData($params);
      }
      else{
        $objPlh = new defaultPlaceholder($this->_model);
        $params['placeholder'] = $placeholder;
        
        return $objPlh->getPlaceholderData($params);
      }
            
      return array();
    }
    
    protected function summ_propis($sum){
      $need_one = false;
      $need_two = false;
      $need_three = false; 
      $need_four = false;   
      
      $str='';
      
      list($grn,$kop) = $this->summ_process($sum);
      
      $str_arr[5] = array('10' => "десять тисяч "
                         ,'11' => "одиннадцять тисяч "
                         ,'12' => "двенадцать тисяч "
                         ,'13' => "тринадцать тисяч "
                         ,'14' => "четырнадцать тисяч "
                         ,'15' => "пятнадцать тисяч "
                         ,'16' => "шестнадцать тисяч "
                         ,'17' => "семнадцать тисяч "
                         ,'18' => "восемнадцать тисяч "
                         ,'19' => "девятнадцать тисяч "
                         ,'20' => "двадцать тисяч "
                         ,'30' => "тридцать тисяч "
                         ,'40' => "сорок тисяч "
                         ,'50' => "пятьдесят тисяч "
                         ,'60' => "шестьдесят тисяч "
                         ,'70' => "семдесят тисяч "
                         ,'80' => "восемдесят тисяч "
                         ,'90' => "девяносто тисяч ");
      
      $str_arr[4] = array('1' => "одна тисяча "
                         ,'2' => "две тисячи "
                         ,'3' => "три тисячи "
                         ,'4' => "четыре тисячи "
                         ,'5' => "пять тисяч "
                         ,'6' => "шесть тисяч "
                         ,'7' => "семь тисяч "
                         ,'8' => "восемь тисяч "
                         ,'9' => "девять тисяч ");
      
      $str_arr[3] = array('1' => "сто "
                         ,'2' => "двести "
                         ,'3' => "триста "
                         ,'4' => "четыреста "
                         ,'5' => "пятьсот "
                         ,'6' => "шестьсот "
                         ,'7' => "семсот "
                         ,'8' => "восемсот "
                         ,'9' => "девятьсот ");
      
      $str_arr[2] = array('10' => "десять "
                         ,'11' => "одиннадцать "
                         ,'12' => "двенадцать "
                         ,'13' => "тринадцать "
                         ,'14' => "четырнадцать "
                         ,'15' => "пятнадцать "
                         ,'16' => "шестьнадцать "
                         ,'17' => "семнадцать "
                         ,'18' => "восемнадцать "
                         ,'19' => "девятнадцать "
                         ,'20' => "двадцять "
                         ,'2' => "двадцать "
                         ,'3' => "тридцать "
                         ,'4' => "сорок "
                         ,'5' => "пятьдесят "
                         ,'6' => "шестьдесят "
                         ,'7' => "семдесят "
                         ,'8' => "восемьдесят "
                         ,'9' => "девяносто ");
      
      $str_arr[1] = array('0' => ""
                         ,'1' => "одна "
                         ,'2' => "две "
                         ,'3' => "три "
                         ,'4' => "четыре "
                         ,'5' => "пять "
                         ,'6' => "шесть "
                         ,'7' => "семь "
                         ,'8' => "восемь "
                         ,'9' => "девять ");
                         
      if(strlen($grn)==6){
        $grn_ = substr($grn,0,1);
        
        $str.= $str_arr[3][$grn_]; 
        
        $grn_ = substr($grn,1,2);
        
        if(isset($str_arr[5][$grn_])){        
          $need_three = true;
          $str.= $str_arr[5][$grn_]; 
          $grn = substr($grn,2);
        }
        else{
          $need_three = true;
          $grn_1 = $grn[1];
          $grn_2 = $grn[2];        
          $str.= $str_arr[2][$grn_1]; 
          $str.= $str_arr[4][$grn_2]; 
          $grn = substr($grn,3);
        }
      }
      
      if(strlen($grn)==5){
        $grn_ = substr($grn,0,2);      
        
        if(($grn%1000)==0){
          $str.= $str_arr[5][$grn_]; 
        }
        elseif(isset($str_arr[5][$grn_])){        
          $need_three = true;
          $str.= $str_arr[5][$grn_]; 
          $grn = substr($grn,2);
        }
        else{
          $need_three = true;
          $grn_1 = $grn[0];
          $grn_2 = $grn[1];
          $str.= $str_arr[2][$grn_1]; 
          $str.= $str_arr[4][$grn_2]; 
          $grn = substr($grn,2);
        }
      }
      
      
      if((strlen($grn)==4) || $need_four){      
        $grn_ = substr($grn,0,2);
        
        if(($grn%1000)==0){
          $grn_ = $grn[0];
          $str.= $str_arr[4][$grn_];
        }
        else{        
          $need_three = true;      
          $grn_ = $grn[0];
          $grn = substr($grn,1);
          $str.= !empty($str_arr[4][$grn_]) ? $str_arr[4][$grn_]:'';
        }
      }
      
      if((strlen($grn)==3) || $need_three){
        if(($grn%100)==0){
          $grn_ = $grn[0];
          $str.= $str_arr[3][$grn_];
        }
        else{
          $need_two = true;
          $grn_ = $grn[0];
          $grn = substr($grn,1);
          $str.= !empty($str_arr[3][$grn_]) ? $str_arr[3][$grn_]:'';
        }      
      }
      
      if((strlen($grn)==2) || $need_two){
        if($grn>=10 && $grn<=20){
          $str.= $str_arr[2][$grn];
        }
        else{
          $need_one = true;        
          $grn_ = $grn[0];
          $grn = $grn[1];
          
          $str.= !empty($str_arr[2][$grn_]) ? $str_arr[2][$grn_]:'';
        }
      }
      
      if((strlen($grn)==1) || $need_one){      
        $str.= $str_arr[1][$grn]; 
      }
      
      $end_str = $str." грн., ".$kop." коп."; 
      
      $bukvica = mb_strtoupper(mb_substr($end_str,0,1, "UTF-8"), "UTF-8");
      $end_str = mb_substr($end_str,1, mb_strlen($end_str)-1, "UTF-8");
      
      $end_str = $bukvica.$end_str;
      
      return $end_str;
    }
    
    protected function summ_process($sum){    
      $temp = explode(".", $sum);   
      
      $grn = $temp[0];
      $kop = empty($temp[1]) ? '0':$temp[1];
      
      if(strlen($kop.'')<2){
        $kop = $kop.'0';
      }
      
      if($kop==0){
        $kop = '00';
      }
      
      return array($grn, $kop);
    }
  
    abstract public function run();
  }
?>
