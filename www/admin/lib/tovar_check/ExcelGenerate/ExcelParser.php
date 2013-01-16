<?php

class ExcelParser{
  
  private $objExcel;
  private $objWorksheet;
  private $_config;
  private $_dataObj;
  
  private $_total_sheets;
  private $highestRow;
  private $highestColumnIndex;
  
  private $_top_pattern = '/^[А-Яа-я]*(\d*)$/is';
  
  public function __construct($excel_path, $config){
      
    $inputFileType = PHPExcel_IOFactory::identify($excel_path);  
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);  
    
    $objReader->setReadDataOnly(true);  
    $this->objExcel = $objReader->load($excel_path);
  
    $this->_total_sheets = $this->objExcel->getSheetCount(); // here 4  
    
    $this->_config = $config;
    $this->_dataObj = new DataObjectLoad();    
  }
  
  /**
  * Запуск парсера
  */
  public function parse(){
    $this->_dataObj->setTotalSheets($this->_total_sheets);
            
    for($sheet = 0; $sheet < $this->_total_sheets; $sheet++){
      $this->objWorksheet = $this->objExcel->setActiveSheetIndex($sheet);
      
      $this->highestRow = $this->objWorksheet->getHighestRow();
      $highestColumn = $this->objWorksheet->getHighestColumn();

      $this->highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

      $this->_dataObj->setActiveSheet($sheet);
      
      $this->setSiteAddress();
      $this->setCurrency();
                  
      
      $this->parseSheetTitle();
      $this->parseTableHead();
            
      $this->parseTableData();      
    }
    
    return $this;
  }
  
  /**
  * Утановить валюту текущей страницы
  */
  private function setCurrency(){
    $this->_dataObj->setCurrency($this->objWorksheet->getCell($this->_config['currency'])->getCalculatedValue());
  }
  
  /**
  * Утановить адрес сайта текущей страницы
  */
  private function setSiteAddress(){
    $this->_dataObj->setSiteAddress($this->objWorksheet->getCell($this->_config['site_address'])->getCalculatedValue());
  }
  
  /**
  * Утановить поисковую систему и регион текущей страницы
  */
  private function parseSheetTitle(){
    $this->_dataObj->setSearchEngine(trim($this->objWorksheet->getTitle()));
  }
  
  /**
  * Вернуть объект данных
  */
  public function getDataObject(){
    return $this->_dataObj;
  }
  
  /**
  * Парсингш заголовка таблицы данных текущей страницы
  */
  private function parseTableHead(){
    $head = array();
    for ($col = 0; $col <= $this->highestColumnIndex; ++$col) {
      $value = trim($this->objWorksheet->getCellByColumnAndRow($col, $this->_config['table_head'])->getValue());
      if(!empty($value)){
        preg_match($this->_top_pattern, $value, $out);        
        $head[$col] = !empty($out[1]) ? $out[1]:0;
      }      
    }

    $this->_dataObj->setSheetHead($head);
  }
  
  /**
  * Парсингш таблицы данных текущей страницы
  */
  private function parseTableData(){
    $sheet_data = array();
    for ($row = $this->_config['start']; $row <= $this->highestRow; ++$row) {
      for ($col = 0; $col <= $this->highestColumnIndex; ++$col) {
        $value = trim($this->objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
        if(empty($value)) continue;
        
        if($col == 0){
          $this->_dataObj->setSheetRequests($value);
        }          
        else{
          $sheet_data[$col] = $value;
        }
      }
      
      $this->_dataObj->setSheetData($sheet_data);
    }
  }
}
?>