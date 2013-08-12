<?php
require_once('iMetaGenerateModel.php');
require_once('MetaGenerateModelCMF.php');
require_once('MetaGenerateModelZend.php');

final class MetaGenerateModelStrategy{
  
  static public function getModel($db){
    if($db instanceof SCMF){
      return new MetaGenerateModelCMF($db);
    }
    if($db instanceof Zend_Db_Adapter_Pdo_Mysql){
      return new MetaGenerateModelZend($db);
    }
    
    return null;
  }
}