<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Konstantin
 * Date: 24.06.13
 * Time: 21:46
 * To change this template use File | Settings | File Templates.
 */
/**
 * Class execute search by Paginator
 *
 * Class ZendCustomExtend_Paginator
 */
class ZendCustomExtend_Paginator implements Zend_Paginator_Adapter_Interface
{
    /**
     * Config
     *
     * @var array
     */
    private $config = array();
    /**
     * Object Execute Elastic
     *
     * @var ExecutElastic
     */
    private $elasticSearch;

    /**
     * Count elements
     *
     * @var integer
     */
    private $count;

    /**
     * Search text
     *
     * @var string
     */
    private $searchText;


    /**
     * Constructor to save execut elastic search
     *
     * @param Helpers_ExecuteElastic $elasticSearch
     * @param array $config
     * @param string $searchText
     */
    public function __construct(Helpers_ExecuteElastic $elasticSearch, array $config, $searchText)
    {
        $this->elasticSearch = $elasticSearch;
        $this->config = $config;
        $this->searchText = $searchText;
    }

    /**
     * Get Items
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @return array|void
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->elasticSearch->setParameters($this->config);
        $results = $this->elasticSearch->runElasticGET(
            $this->searchText,
            $this->config['convert_to_array'],
            "executeMatch",
            $offset,
            $itemCountPerPage
        );

        return $results;
    }

    /**
     * Count main pages
     *
     * @return int|void
     */
    public function count()
    {
        $this->elasticSearch->setParameters($this->config);

        return $this->elasticSearch->runElasticGET(
            $this->searchText,
            $this->config['total_hits'],
            "executeMatch"
        );
    }
}