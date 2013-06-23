<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 30.05.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

/**
 * Controller for execute Elastic Search
 *
 * Class ElasticSearchController
 */
class ElasticsearchController extends App_Controller_Frontend_Action
{
    /**
     * Config parameters
     *
     * @var objects
     */
    private $config;

    /**
     * Initialize system controller for get request
     */
    public function init()
    {
        parent::init();

        $this->config = Zend_Registry::get("config");

        if (empty($this->config)) {
            throw new Exception("Error: configuration is not include class: " . __CLASS__ . ", line: " . __LINE__);
        }
    }

    /**
     * Action for execute elastic search
     */
    public function indexAction()
    {
        $term = $this->_getParam("term");

        if (empty($term)) {
            $this->_helper->json($term);
        }
        $parameters = $this->config->toArray();

        $helpersElasticExecute = $this->_helper->helperLoader("ExecuteElastic");
        $results = $helpersElasticExecute->runElasticGET($parameters['search_engine'], $term);

        if (empty($results)) {
            $this->_helper->json($results);
        }

        $executeElastic = $this->_helper->helperLoader("ExecuteElastic");

        $this->_helper->json($executeElastic->executeFormatData(
            $results, $this->currency,
            $this->_helper->helperLoader("Prices_Recount"),
            $this->_helper->helperLoader("Prices_Discount"),
            true));
    }
}