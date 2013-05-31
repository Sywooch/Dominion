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
class ElasticSearchController extends App_Controller_Frontend_Action
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
        $action = $this->_getParam("event");
        $query = $this->_getParam("term");

        if (empty($query) && $action != "PUT") {
            echo $query;

            return;
        }
        $formatQuery = new ContextSearch_FormatQuery(
            $this->config->search_engine->name,
            $action,
            $this->config->search_engine->index
        );

        if ($action == "PUT") {
            $formatQuery->setData($this->prepareDataForPut());
        }

        $formatQuery->setHost($this->config->search_engine->host);
        $formatQuery->setType($this->config->search_engine->type_products);
        $formatQuery->setQuery($query);

        $queryObject = new ContextSearch_Query();
        $queryObject->execQuery($formatQuery);

        echo $queryObject->convertToJSON();

        return;
    }

    /**
     * Genarate for elastic data
     *
     * @return array
     */
    private function prepareDataForPut()
    {
        $formatDataElastic = $this->_helper->helperLoader("FormatDataElastic");

        return $formatDataElastic->formatDataForElastic();
    }
}