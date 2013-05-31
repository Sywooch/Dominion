<?php

/**
 * Class Execute
 *
 * @package library\ContextSearch\Elastic
 */
class ContextSearch_Elastic_Execute implements ContextSearch_ExecuteInterface
{
    private $fields = array("NAME_PRODUCT", "BRAND");
    private $actions = array("GET", "PUT", "DELETE");

    /**
     * Execute function for query elastic
     *
     * @param library_ContextSearch_FormatQuery $format_query
     *
     * @return mixed
     * @throws Exception
     */
    public function exec(ContextSearch_FormatQuery $format_query)
    {
        $elastic_factory = new ContextSearch_Elastic_ElasticSearchFactory($format_query);
        $elastic_model = $elastic_factory->getElasticSearchModel();
        $logic = $format_query->getAction();

        if (!in_array($logic, $this->actions))
            throw new \Exception("Exception: The actions is not exists");

        switch ($logic) {
            case 'GET':
                $elastica_query = $elastic_factory->getElasticaQuery();
                $elastic_model->facetsData($elastic_factory->getFacets(), $elastica_query, $format_query->getIndex(), $this->fields);
                $result_query = $elastic_model->searchInElastic($elastica_query, $elastic_factory->getQueryString(), $format_query->getQuery());
                break;
            case 'PUT':
                $result_query = $elastic_model->putData($elastic_factory->getDocument(), $format_query->getData(), $format_query->getType());
                break;
            case 'DELETE':
                $result_query = $elastic_model->deleteType($format_query->getType());
                break;
        }

        return $result_query;
    }

    /**
     * Получить массив из результата поиска
     *
     * @param Array $result_query
     *
     * @return type
     * @throws \Exception
     */
    public function getArray($result_query)
    {
        if (empty($result_query))
            throw new \Exception("Exception: The result_query is empty");

        $arr = array();

        foreach ($result_query as $result) {
            $arr[] = $result->getData();
        }

        return $arr;
    }

    /**
     * Получить JSON из результата поиска
     *
     * @param type $result_query
     *
     * @return type
     * @throws \Exception
     */
    public function getJSON($result_query = null)
    {
        $arr = $this->getArray($result_query);

        return json_encode($arr);
    }

    /**
     * Получить XML из результата поиска
     *
     * @param type $result_query
     *
     * @throws \Exception
     */
    public function getXML($result_query = null)
    {
        $arr = $this->getArray($result_query);
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->appendChild($dom->createElement("root"));
        foreach ($arr as $key => $value) {
            $node = $root->appendChild($dom->createElement("node" . $key));
            foreach ($value as $key_val => $val) {
                $node->appendChild($dom->createElement($key_val, $val));
            }
        }

        return $dom->saveXML();
    }
}