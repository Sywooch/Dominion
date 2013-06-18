<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */

class ContextSearch_ElasticSearch_BuildExecute_GET extends ContextSearch_ElasticSearch_BuildExecute_QueryAbstract
{
    /**
     * Constructor for connect
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     */
    public function __construct(ContextSerch_ElasticSearch_Connect $connect)
    {
        parent::__construct($connect);
    }

    /**
     * Execute GET query
     */
    public function execute()
    {

    }
}