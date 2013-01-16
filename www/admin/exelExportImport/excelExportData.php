<?php

require_once 'lib/ExportToExcel.class.php';
require_once 'itemCreate.php';

/**
 * Starter export / import
 *
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
class excelExportData {

    /**
     *
     * @param mixed $catalogueId
     * @return \ExportToExcel 
     */
    static function export($catalogueId) {
//        $data = new itemCreate(new SCMF());

        $export = new ExportToExcel(new itemCreate(new SCMF()));

        $export->addPage($catalogueId);
        $export->setHeaders();
        $export->setItems();

        return $export;
    }

}