<?php

  class tovar_check extends check_rashod{
    
    /**
    * put your comment there...
    * 
    * @param integer $zakaz_id
    */
    
    public function run(){
      $config = $this->_config->getCustomData();
      
      $this->processCarcasData();
      $this->processTableData($config['table_body']);      
    }            
  }
?>