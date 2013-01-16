<?php

/**
 * Класс для генерации Excel файлов 
 * Применяется для проектов где необходимо выгрузить товар в Excel
 *
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */

require_once "config.php";

class ExportToExcel {

    /**
     * Active row into sheet
     * @var integer 
     */
    private $_row = 1;
    private $_xls;
    private $_pages = 0;

    /**
     * DI data
     * @var iItems 
     */
    private $_data;

    public function __construct(iItems $data) {
        $this->_data = $data;
        $this->_xls = new PHPExcel();
    }

    public function setHeaders() {


        $styleArray = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FFFF0000'),
                ),
            ),
            'font' => array(
                'bold' => true,
            )
        );

        $this->_xls->getSheet()->getStyleByColumnAndRow(1, 1)->applyFromArray($styleArray);
        $this->_xls->getActiveSheet()->freezePaneByColumnAndRow(0, 2);

        $columns = $this->_data->getColumns();

        for ($i = 0; $i < count($columns); $i++) {
            $this->_xls->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
            $this->_xls->getActiveSheet()->getStyleByColumnAndRow($i, $this->_row)->applyFromArray($styleArray);
            $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($i, $this->_row, $columns[$i]);
        }


        $this->_row++;
    }

    public function setItems() {

        $items = $this->_data->getItems();

        foreach ($items as $value) {

            for ($j = 0; $j < count($value); $j++)
                $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($j, $this->_row, $value[$j]);

            $this->_row++;
        }
    }

    /**
     * 
     * @param mixed $catalogueId 
     */
    public function addPage($catalogueId) {

        $name = $this->_data->getPageName($catalogueId);

        if (empty($name))
            throw new Exception("Catalog id {$catalogueId} haven't founded.");

        if ($this->_pages > 0) {
            $this->_xls->createSheet($this->_pages);
            $this->_xls->setActiveSheetIndex($this->_pages);
        }

//        $name = mb_substr($name, 0, 31, $this->_data->getEncoding());

        $this->_xls->getActiveSheet()->setTitle(mb_substr($name, 0, 31, $this->_data->getEncoding()));

        $this->_pages++;
    }

    public function getFile($fileName) {
// Очищаем вывод в буфер
        ob_clean();
        header('Content-Type: application/vnd.ms-excel');
//	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->_xls, 'Excel5');

//	$objWriter = new PHPExcel_Writer_Excel5($this->_xls);

        $objWriter->save('php://output');

        die();
    }

}