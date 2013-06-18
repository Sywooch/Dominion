<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:04
 * To change this template use File | Settings | File Templates.
 */

class ContextSearch_ElasticSearch_BuildExecute_PUT extends ContextSearch_ElasticSearch_BuildExecute_QueryAbstract
{
    /**
     * Document Elastic Search
     *
     * @var object
     */
    private $type;

    /**
     * Limit constant
     */
    const LIMIT_DOCS = 500;

    /**
     * Create Query
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     */
    public function __construct(ContextSearch_ElasticSearch_Connect $connect)
    {
        parent::__construct($connect);

        $this->type = $this->getType();
    }

    /**
     * Add Documents
     *
     * @param array $data
     */
    public function addDocuments(array $data)
    {
        foreach ($data as $key => $item) {
            $documents[] = new \Elastica\Document($key, $item);

            if (count($documents) === self::LIMIT_DOCS) {
                $this->type->addDocuments($documents);
                $this->execute();
                unset($documents);
            }
        }
    }

    /**
     * Execute PUT query
     *
     * @return mixed|void
     */
    public function execute()
    {
        $this->type->getIndex()->refresh();
    }
}