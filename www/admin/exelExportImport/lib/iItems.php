<?php

/**
 * Interface for data to Excel exporr 
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
interface iItems {

    /**
     * Return items array 
     * @return Array
     */
    public function getItems();

    /**
     * Get name of a sheet page 
     * 
     * @param mixed $id
     */
    public function getPageName($id);

//    public function getPageName($catalogueId);

    public function getEncoding();

    
    /**
     * @return array 
     */
    public function getColumns();
}

