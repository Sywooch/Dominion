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
     * @param null                   $page
     * @param                        $perPage
     * @param Helpers_ExecuteElastic $elasticExecute
     * @param array                  $config
     * @param                        $search_text
     */
    public function setElements($page = null, $perPage, Helpers_ExecuteElastic $elasticExecute, $config, $search_text)
    {
        if (empty($page)) {
            $page = 1;
        }

        $this->paginator = new Zend_Paginator(new ZendCustomExtend_Paginator($elasticExecute, $config, $search_text));

        $this->paginator->setCurrentPageNumber($page);
        $this->paginator->setItemCountPerPage($perPage);

        $this->amount = $this->paginator->getPages()->totalItemCount;
        $this->page = $this->paginator->getCurrentPageNumber();

        $this->end = $this->paginator->getPages();
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
        return $this->end->pageCount;
    }

    /**
     * Get Currency item
     *
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->paginator->getCurrentItems();
    }
}