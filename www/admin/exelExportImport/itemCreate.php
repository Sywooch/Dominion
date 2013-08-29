<?php

require_once 'lib/iItems.php';
require_once 'modelPrice.class.php';

/**
 * Description of itemCreate
 *
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
class itemCreate implements iItems {

    const URL = 'http://7560000.com.ua';
    const STATUS_ACTIVE = 'Active';
    const STATUS_PAUSED = 'Paused';
    const STATUS_NEW = 1;
    const STATUS_OLD = '';
    const TEXT_ALL_CATALOG = 'Выгрузка всего каталога';
    const ENCODING = 'utf-8';
    
     /**
     * разница в днях который воспринимаем как новый товар 
     */
    const DIFF_DAY = 10;

    /**
     * Model data
     * @var modelPrice
     */
    private $_model;
    private $_catalogueId = null;
    private $_colNames = array('Name', 'Brand', 'Status', 'URL', 'New items' , 'Item type', 'Article', 'Price');

    public function __construct(SCMF $cmf) {
        $this->_model = new modelPrice($cmf);
    }

    private function setCatalogueId($catalogueId) {
        $this->_catalogueId = $catalogueId;
    }

    public function getItems() {

        $itemsArray = array();

        $ctaloguesIDs = $this->_model->getCatalogesId($this->_catalogueId);
        foreach ($ctaloguesIDs as $catalogIter) {
            $items = $this->_model->getItems($catalogIter['CATALOGUE_ID']);

            if (empty($items))
                continue;

            foreach ($items as $value) {

                $itemsNew = Array();
                $itemsNew[] = trim($value['NAME']);
                $itemsNew[] = trim($value['BRAND_NAME']);
                $itemsNew[] = $this->getStatus($value);
                $itemsNew[] = $this->clearURl($value);
                $itemsNew[] = $this->getStatusNew($value['DAY_DIFF']);
                $itemsNew[] = $value['TYPENAME'];
                $itemsNew[] = $value['ARTICLE'];
                $itemsNew[] = $value['PRICE'];
                $itemsArray[] = $itemsNew;
            }
        }


        return $itemsArray;
    }

    private function clearURl($item) {
        $pattern[0] = '/&amp;/';
        $pattern[1] = '/amp;/';
        $pattern[2] = '/&quot;/';
        $pattern[3] = '/&#039;/';

        $replace[0] = '&';
        $replace[1] = '';
        $replace[2] = '';
        $replace[3] = '';

        $item['NAME'] = trim($item['NAME']);
        $item['NAME'] = preg_replace($pattern, $replace, $item['NAME']);

        $urname = preg_replace('/[^\w]/', '-', $this->_model->getSCMF()->translit($item['NAME']));
        $urname = preg_replace("/-{2,}/", "-", $urname);

        return self::URL . '/item/' . $item['ITEM_ID'] . '/' . $urname . '/';
    }

    /**
     * Вернуть статус
     * @param Array $item
     * @return String
     */
    private function getStatus(Array $item) {
        if ($item['STATUS'] == 0 || $item['PRICE'] == 0)
            return self::STATUS_PAUSED;

        return self::STATUS_ACTIVE;
    }

    public function getEncoding() {
        return self::ENCODING;
    }

    public function getColumns() {
        return $this->_colNames;
    }

    /**
     * Check exist ID catalogue
     * 
     * @param mixed $catalogueId
     * @return boolean 
     */
    private function checkCatalogID($catalogueId) {

        $catalogueId = (int) $catalogueId;
        if ($this->_model->checkCatalog($catalogueId)) {
            $this->setCatalogueId($catalogueId);
            return true;
        }

        return false;
    }

    /**
     * Вернуть массив каталогов потомков
     * @param integer $catalogId 
     * @return array
     */
    private function getCatalogsByParent($catalogId) {

        $ctaloguesIDs = $this->_model->getCatalogesId($catalogId);
        $CatArray = array();
        foreach ($ctaloguesIDs as $catalogIdIter)
            array_push($CatArray, $catalogIdIter['CATALOGUE_ID']);

        return $CatArray;
    }

    /**
     * Get catalogue ID
     * 
     * @param type $catalogueId
     * @return string 
     */
    public function getPageName($catalogueId) {
        if ($catalogueId === 'all'){
            $this->setCatalogueId(-1);
            return self::TEXT_ALL_CATALOG;
        }

        if ($this->checkCatalogID($catalogueId))
            return $this->_model->getCatalogName($catalogueId);
        else
            return null;
    }

    private function getStatusNew($param) {

        if (is_null($param))
            return self::STATUS_OLD;
        
        if ($param < self::DIFF_DAY)
            return self::STATUS_NEW;
        else
            return self::STATUS_OLD;
    }

    /**
     *
     * @param array $itemAtributs
     * @return array 
     */
    private function getArrayTemplateForItem(array $itemAtributs) {
        $attributes = $this->_model->getAttributs($this->getCatalogsByParent($this->_idCatalog));

        $tmp = array();

        foreach ($attributes as $attr) {
//	    $h = array_search($attr['ATTRIBUT_ID'], $itemAtributs[0]);
            $value = "";
            foreach ($itemAtributs as $itemAttribut) {
                if ($itemAttribut['ATTRIBUT_ID'] == $attr['ATTRIBUT_ID'])
                    $value = $itemAttribut['VALUE'];
            }
            array_push($tmp, $value);
        }
        return $tmp;
    }

}
