<?php
class export_shopuser{
    protected $_cells;

    /**
    * put your comment there...
    * 
    * @var checkModel
    */
    protected $_model;

    function __construct(shopuserModel $model){
        $this->_model = $model;      
        $this->_cells = new SplObjectStorage();
    }

    /**
    * put your comment there...
    * 
    * @return \SplObjectStorage
    */

    public function getExcelData(){
        return $this->_cells;
    }

    public function run(){
        $result = $this->_model->getShopUsers();
        if(!empty($result)){
            $y=0;
            foreach($result as $row){
                $excelCell = new ExcelData();
                $excelCell->setCoord(array(0,$y));
                $excelCell->setValue($row['EMAIL']);
                $this->_cells->attach($excelCell);

                $excelCell = new ExcelData();
                $excelCell->setCoord(array(1,$y));
                $excelCell->setValue($row['name']);
                $this->_cells->attach($excelCell);
                $y++;
            }
        }
    }
}