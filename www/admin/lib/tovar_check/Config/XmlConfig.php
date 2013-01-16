<?php

require_once 'ExcelData.php';
require_once 'iConfig.php';

class XmlConfig implements iConfig {

  const PATH_CELL_COORD = '/config/cell';
  const PATH_CUSTOMIZE = '/config/custom';
  const ATTRIBUTE_COORD = 'coord';
  const ATTRIBUTE_PLACEHOLD = 'placeholder';
  const ATTRIBUTE_WRAP = 'wrap';
  const ATTRIBUTE_IMAGE = 'image';
  const ATTRIBUTE_WIDTH = 'width';
  const ATTRIBUTE_HEIGHT = 'height';
  const ATTRIBUTE_AUTOFIT = 'autofit';

  /**
   * Храним данны о ячейках
   * @var \SplObjectStorage
   */
  private $_cells;

  /**
   * Храним кстомерные параметры как ключ => значение
   * @var array 
   */
  private $_custom;
  
  public function __construct() {
    $this->_cells = new SplObjectStorage();
  }

  private function validate(DOMDocument $dom)
  {
    libxml_use_internal_errors(true);
    if (!$dom->schemaValidate(dirname(__FILE__).'/services.xsd'))
    {
      throw new InvalidArgumentException(implode("\n", $this->getXmlErrors()));
    }
    libxml_use_internal_errors(false);
  }
  
  /**
   * Собираем статичные значения ячеек
   * @param \SimpleXMLElement $cells 
   */
  private function setCell($cells) {

    foreach ($cells as $key=>$value) {
      $excelCell = new ExcelData();
      $excelCell->setValue((string) $value->text);
      
      if(isset($value->style)){
        $style = $this->processingStyle($value->style);
//        var_dump($style['style']);
//        exit;
        $excelCell->setStyle($style['style']);  
      }
      

      /* @var  $value \SimpleXMLElement */
      foreach ($value->attributes() as $atributName => $atributeValue) {

        /* @var  $atributeValue \SimpleXMLElement */
        if ($atributName == self::ATTRIBUTE_COORD)
          $excelCell->setCoord((string) $atributeValue);

        // Если значение в ячейке - это плейсходер который надо будет потом заменить
        if ($atributName == self::ATTRIBUTE_PLACEHOLD)
          $excelCell->setIsPlaceholder(TRUE);
          
        if ($atributName == self::ATTRIBUTE_WRAP)
          $excelCell->setWrap(TRUE);
          
        if ($atributName == self::ATTRIBUTE_IMAGE)
          $excelCell->setImage(TRUE);
          
        if ($atributName == self::ATTRIBUTE_WIDTH)
          $excelCell->setWidth((int) $atributeValue);
                                  
        if ($atributName == self::ATTRIBUTE_HEIGHT)
          $excelCell->setHeight((int) $atributeValue);
          
        if ($atributName == self::ATTRIBUTE_AUTOFIT)
          $excelCell->setAutofit(TRUE);
      }

      $this->_cells->attach($excelCell);
    }
  }
  
  private function processingStyle($obj){
    foreach($obj as $key=>$value){      
      if(count($value) > 0){        
        $style[$key] = $this->processingStyle($value);
      }
      foreach ($value->attributes() as $atributName => $atributeValue) {
        $style[$key][$atributName] = (string) $atributeValue;
      }
    }
    
    return $style;
  }

  /**
   * Собираем все кастомерные данные
   * @param \SimpleXMLElement $cells
   */
  private function setCustom($cells) {
    foreach ($cells as $value)
      foreach ($value as $custovParam)
        $this->_custom["{$custovParam->getName()}"] = (string) $custovParam;
  }

  public function parse($filePath) {

    try {

      /* @var  $configXml \SimpleXMLElement */
      $configXml = simplexml_load_file($filePath);

      // Получаем координаты
      $this->setCell($configXml->xpath(self::PATH_CELL_COORD));

      // Получаем кастомерные значения
      $this->setCustom($configXml->xpath(self::PATH_CUSTOMIZE));



      return $this;
    } catch (Exception $e) {
      echo $e->getMessage();
      echo $e->getTrace();
    }
  }

  /**
   * Получить данные по статическим координатам
   * @return \SplObjectStorage
   */
  public function getCellsData(){
    return $this->_cells;
  }
  
  /**
   * Информация из узла <custom>
   * @return array
   */
  public function getCustomData(){
    return $this->_custom;
  }
  

}