<?php

/**
 * Интрефейс для определения методов котрые можно будет использывать в вызове
 * конфигуратора
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
interface iConfig {

  public function parse($filePath);
  
  
  public function getCellsData();

  /**
   * Информация из узла <custom>
   * @return array
   */
  public function getCustomData();
}

