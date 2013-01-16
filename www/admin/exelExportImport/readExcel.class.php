<?php

require_once 'modelPrice.class.php';
require_once 'phpexcel/Classes/PHPExcel.php';

/**
 * Description of readExcel
 *
 * @author Rus
 */
class readExcel {

    /**
     * DB Model
     * @var modelPrice 
     */
    private $_model;

    /**
     *
     * @var PHPExcel 
     */
    private $_xls;
    private $_error = null;

    public function __construct(SCMF $cmf) {
        $this->_model = new modelPrice($cmf);
//        $this->_xls = new PHPExcel();
    }

    public function getError() {
        return $this->_error;
    }

    public function run() {
        $this->insertItem();
    }

    /**
     * Установка читаемого файла
     * @param type $inputFileName 
     */
    public function setFile($inputFileName) {
        try {
            $typeFile = PHPExcel_IOFactory::identify($inputFileName);

            if ($typeFile != 'Excel5' && $typeFile != 'Excel2007')
                throw new Exception("Файл должен быть формата Excel");

            $objReader = PHPExcel_IOFactory::createReader($typeFile);



            $objReader->setReadDataOnly(true);
            $this->_xls = $objReader->load($inputFileName);
            return true;
        } catch (Exception $exc) {
            echo $exc->getMessage();
            return false;
        }
    }

//    private function updatePrice(array $item) {
//	
//    }

    private function insertItem() {
        $objWorksheet = $this->_xls->setActiveSheetIndex(0); // first sheet  

        $highestRow = $objWorksheet->getHighestRow(); // here 5  
//	$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'  
        $item = array();
        for ($row = 2; $row <= $highestRow; ++$row) {
            try {
                $item['ID'] = (int) $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                if (!$item['ID'])
                    throw new Exception("Не обнаружен корректный ID товара строка {$row}");
                $item['PRICE'] = $objWorksheet->getCellByColumnAndRow(6, $row)->getCalculatedValue();
                $item['PRICE1'] = $objWorksheet->getCellByColumnAndRow(7, $row)->getCalculatedValue();
//                $g = $this->_model->updatePrice($item);
            } catch (Exception $e) {
                echo $e->getMessage() . "<br/>";
            }
        }
    }

}