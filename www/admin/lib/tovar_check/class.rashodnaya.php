<?php

  class rashodnaya extends check_rashod{
    
    /**
    * put your comment there...
    * 
    * @param integer $zakaz_id
    */
    
    public function run(){
      $config = $this->_config->getCustomData();
      
      $this->processCarcasData();
      $this->processTableData($config['table_body']);
      $this->processTableData($config['table_body_two']);
    }            
    
    protected function underTable($row, $summ){
      $row++;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(4, $row));
      $excelCell->setValue('Вcего на сумму');      
      $excelCell->setHeight(12);      
      $this->attach($excelCell);
      
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(5, $row));
      $excelCell->setValue($summ);      
      $excelCell->setHeight(12);
      $this->attach($excelCell);
      
      $row = $row + 2;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(0, $row));
      $excelCell->setValue('Вcего на сумму');      
      $excelCell->setHeight(12);
      $this->attach($excelCell);
      
      $row++;
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(0, $row));
      $excelCell->setValue($this->summ_propis($summ));      
      $excelCell->setHeight(12);
      $this->attach($excelCell);
      
      $row = $row + 2;
      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(0, $row));
      $excelCell->setValue('Выписал');      
      $excelCell->setHeight(12);
      $this->attach($excelCell);

      $excelCell = new ExcelData();          
      $excelCell->setCoord(array(3, $row));
      $excelCell->setValue('Получил(а)');
      $excelCell->setHeight(12);
      $this->attach($excelCell);
      
      $row++;
      $excelCell = new ExcelData();        
      $excelCell->setCoord(array(1, $row));
      $excelCell->setValue($this->_model->getSettings('tovar_check_vipisal'));
      $excelCell->setHeight(12);
      $this->attach($excelCell);
    }
  }
?>