<?php

require_once 'iExcelData.php';

/**
 * Object for Excel data
 *
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
class ExcelData implements iExcelData {

  /**
   * Type of coordinates
   * 0 - like 'A2'
   * 1 - like an array (1,4) - cell by row = 1 and collumn = 4
   * @var int 
   */
  private $_type = null;

  /**
   * Data cell
   * @var mixed 
   */
  private $_value = null;

  /**
   * Coordinate of cell
   * @var mixed 
   */
  private $_coord = null;

  /**
   * Collect errors
   * @var array 
   */
  private $_error = array();

  /**
   * Style of cell
   * @var array 
   */
  private $_style = array();
  
  /**
  * Wraping the text
  * 
  * @var boolean
  */
  private $_wrap = FALSE;  
  
  /**
  * The imagein the xell
  * 
  * @var boolean
  */
  private $_image = FALSE;
  
  /**
  * Cell width
  * 
  * @var integer
  */
  private $_width = null;
  
  /**
  * Cell height
  * 
  * @var integer
  */
  private $_height = null;
  
  private $_autofit = FALSE;
  
  private $_isPlaceholder = false;

  public function setIsPlaceholder($type) {

    if (!empty($type))
      $this->_isPlaceholder = TRUE;
    else
      $this->_isPlaceholder = FALSE;
  }

  public function isPlaceholder() {
    return $this->_isPlaceholder;
  }
  
  public function isImage() {
    return is_null($this->_image) ? false:true;
  }

  /**
   * 
   * @param array $param  
   * array(
   *     'borders' => array(
   *                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
   *                                           'color' => array('argb' => 'FFFF0000'),
   *                                          ),
   *                        ),
   *      'font' => array('bold' => true,)
   *      );
   * @return ExcelData
   */
  public function setStyle(array $style) {
    $this->_style = $style;
    return $this;
  }

  /**
   *
   * @param mixed $value 
   * @return ExcelData
   */
  public function setValue($value) {
    $this->_value = $value;
    return $this;
  }
  
  public function setWrap($wrap) {
    $this->_wrap = $wrap;
    return $this;
  }
  
  public function setAutofit($autofit) {
    $this->_autofit = $autofit;
    return $this;
  }
  
  public function setImage($image) {
    $this->_image = $image;
    
    $this->_type = 3;

    return $this;
  }
  
  public function setWidth($width) {
    $this->_width = $width;
    return $this;
  }
  
  public function setHeight($height) {
    $this->_height = $height;
    return $this;
  }

  /**
   * Передаем в любом формате
   * или 'A2' или массив вида [1,4]
   * @param mixed $coord
   * @throws Exception
   * @example array(1,2)
   * @example 'A2' 
   * @return ExcelData
   */
  public function setCoord($coord) {
    try {
      if ($this->setTypeCoord($coord)) {
        /* TODO: Здесь можно прописать обработчик проверки нет ли такой уже кооддинаты и т.д.
         * пока оставил тупо присвоение
         */
        $this->_coord = $coord;
      }
      else
        throw new Exception('Get wrong coordinate for excel cell');
    } catch (Exception $e) {
      $this->_error[] = $e->getTraceAsString();
    }
    return $this;
  }

  private function setTypeCoord($coord) {
    //Выясняем что за координата пришла
    // Если строка
    if (is_string($coord)) {
      $this->_type = 1;
        
      return TRUE;
    }

    // Если массив и в нем чётко две позиции цифрового типа
    // То всё зашибись
    //Иначе - возвращаем false
    if (is_array($coord) && count($coord) === 2) {

      if (!is_int($coord[0]))
        return false;
      if (!is_int($coord[1]))
        return false;

      $this->_type = 2;
        
      return TRUE;
    }

    // Во всех остальных случаях
    return FALSE;
  }

  public function getType() {
    return $this->_type;
  }

  public function getStyle() {
    return $this->_style;
  }

  /**
   * Errors
   * @return array 
   */
  public function getErrors() {
    return $this->_error;
  }

  public function getValue() {
    return $this->_value;
  }
  
  public function getWrap() {
    return $this->_wrap;
  }
  
  public function getAutofit() {
    return $this->_autofit;
  }
  
  public function getImage() {
    return $this->_image;    
  }
  
  public function getWidth() {
    return $this->_width;
  }
  
  public function getHeight() {
    return $this->_height;
  }

  /**
   * Get coordinates of excel cell
   * @return mixed 
   */
  public function getCoord() {
    return $this->_coord;
  }

}
