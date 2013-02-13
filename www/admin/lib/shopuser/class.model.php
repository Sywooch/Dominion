<?php
  class shopuserModel{
    
    /**
    * put your comment there...
    * 
    * @var SCMF
    */
    private $_cmf;
    
    function __construct(SCMF $cmf){
      $this->_cmf = $cmf;
    }

    public function getShopUsers(){
      $sql="select EMAIL
                 , concat (SURNAME,' ' ,NAME) as name
           from SHOPUSER
           where length(EMAIL) > 0";
           
      return $this->_cmf->select($sql);
    }

  }
?>