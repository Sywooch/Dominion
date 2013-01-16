<?php

// Подключаем внешнюю библиотеку PHPExcel
//require_once ROOT_PATH . '/lib/phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'iExcelData.php';

/**
 * Генератор отчетов в формате Excel 
 */
class ExcelReportGenerate {
  /**
   * Контейнер с данными для страницы Excel
   * @var \SplObjectStorage 
   */
//  private $_dataObj;
//  private $_config;

  /**
   * Excel генератор
   * @var \PHPExcel 
   */
  private $_objExcel;

  // TODO: Не знаю что это такое. надо спросить у Димы

  /**
   * Счётчик страниц
   * @var int
   */
  private $_pages = 0;

  public function __construct() {
    $this->_objExcel = new PHPExcel();
  }

  /**
   * Установить коллекцию данных
   * 
   * @param \SplObjectStorage $dataObj
   */
//  public function setObjData(\SplObjectStorage $dataObj) {
//    $this->_dataObj = $dataObj;
//  }

  /**
   * Запуск формирования ячеек с данными
   * @param iExcelData $value
   */
  public function setSpreadsheetData(SplObjectStorage $dataObj) {

    // Для всех объектов из коллеции делаем обработку данных
    // Вставляем данных по координатам
    $i=1;
    foreach ($dataObj as $value) {
      $row_index = 0;
      
      if (!($value instanceof iExcelData))
        throwException('Data is not iExcelData');
      
//        $this->setCell($value);
      $coord = $value->getCoord();
      
      if($value->getHeight()){
        $row_index = $this->getCellRowCoord($coord);
      }
      
      switch ($value->getType()) {
        case 1:
          if($value->getWidth()){
            $this->_objExcel->getActiveSheet()->getColumnDimension($coord)->setWidth($value->getWidth());
          }
          else{
            $this->_objExcel->getActiveSheet()->setCellValue($coord, $value->getValue());  
          }
          
          if($value->getStyle()){
            $this->_objExcel->getActiveSheet()->getStyle($coord)->applyFromArray($value->getStyle());  
          }
          
          if($value->getWrap()){
            $this->_objExcel->getActiveSheet()->getStyle($coord)->getAlignment()->setWrapText(true);  
          }
        break;

        case 2:          
          $this->_objExcel->getActiveSheet()->setCellValueByColumnAndRow($coord[0], $coord[1], $value->getValue());
        break;
        
        case 3:
          $this->drawImage($coord, $value->getValue());
        break;
      }
      
      if(!empty($row_index)){
        $this->_objExcel->getActiveSheet()->getRowDimension($row_index)->setRowHeight($value->getHeight());
      }
    }
  }
  
  private function getCellRowCoord($coord){
    if (is_string($coord)) {
      $pattern = '/^(\D*)(\d*)$/is';
      preg_match($pattern, $coord, $out);
        
      return !empty($out[2]) ? $out[2]:0;
    }

    if (is_array($coord) && count($coord) === 2) {        
      return (int)$coord[0];
    }
    
    return 0;
  }
  
  private function drawImage($coord, $image){
    $objDrawing = new PHPExcel_Worksheet_Drawing();

    $objDrawing->setPath($image);
    
    $objDrawing->setCoordinates($coord);
     
    $objDrawing->setWorksheet($this->_objExcel->getActiveSheet());
  }

  /**
   * Формирования ячейки с данными
   * 
   * @param iExcelData $value
   */
//  private function setCell($value) {
//    switch ($value->getType()) {
//      case 1:
//        $this->_objExcel->getActiveSheet()->setCellValue($value->getCoord(), $value->getValue());
//        break;
//
//      case 2:
//        $coord = $value->getCoord();
//        $this->_objExcel->getActiveSheet()->setCellValueByColumnAndRow($coord[0], $coord[1], $value->getValue());
//        break;
//    }
//  }

  /**
   * Получить сгенерированный файл
   * 
   * @param string $name
   */
  public function getFile($name) {
//    ob_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $name . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->_objExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

  /**
   * Создать новую страницу
   * 
   * @param string $name
   */
  public function addPage($name) {
    if ($this->_pages > 0) {
      $this->_objExcel->createSheet($this->_pages);
      $this->_objExcel->setActiveSheetIndex($this->_pages);
    }
    $this->_objExcel->getActiveSheet()->setTitle($name);

    $this->_pages++;
  }

}