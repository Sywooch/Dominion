<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Konstantin
 * Date: 23.06.13
 * Time: 19:39
 * To change this template use File | Settings | File Templates.
 */

class Helpers_CustomPaginator extends App_Controller_Helper_HelperAbstract
{
    /**
     * End
     *
     * @var integer
     */
    private $end;

    /**
     * Anmount pages
     *
     * @var integer
     */
    private $amount;

    /**
     * Number page
     *
     * @var integer
     */
    private $page;

    /**
     * Paginator Zend
     *
     * @var Paginator
     */
    private $paginator;

    /**
     * Set Element
     *
     * @param array $data
     * @param integer $page
     * @param integer $perPage
     */
    public function setElements(array $data, $page, $perPage)
    {
        $this->paginator = Zend_Paginator::factory($data);

        $this->paginator->setCurrentPageNumber($page);
        $this->paginator->setItemCountPerPage($perPage);


        $amount = $this->paginator->getPages()->totalItemCount;
        $this->page = $page > ceil($amount / $perPage) ? ceil($amount / $perPage) : $page;
        $this->end = ceil($amount / $perPage);
    }

    /**
     * Getter for page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Getter for amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Getter for end
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get Currency item
     *
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->paginator->getCurrencyItems();
    }
}