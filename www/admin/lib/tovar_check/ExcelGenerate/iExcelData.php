<?php

/**
 * Interface for Data Excel
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
interface iExcelData {
  /**
   * Get a coord  
   */
  public function getCoord();

  public function getStyle();
  
  public function getValue();
  
  public function isPlaceholder();


  /**
   * Get a type of coord 
   */
  public function getType();
  
}