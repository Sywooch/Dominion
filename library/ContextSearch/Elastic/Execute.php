<?php
use Elastica\ResultSet;

/**
 * Class Execute
 *
 * @package library\ContextSearch\Elastic
 */
class ContextSearch_Elastic_Execute implements ContextSearch_ExecuteInterface
{
    /**
     * Name method for execute into elastic search
     *
     * @var array
     */
    private $actions = array("GET", "PUT", "DELETE");

    /**
     * Execute function for query elastics
     *
     * @param ContextSearch_FormatQuery $format_query
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
                $result_query = $elastic_model->searchInElastic(
                    $elastic_factory->getQueryPrefix(), $format_query->getNameFields(), $elastic_factory->getElasticaQuery(), $elastic_factory->getQueryString(), $format_query->getQuery()
                );
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
     * @param ResultSet $result_query
     *
     * @return array
     */
    public function getArray(ResultSet $result_query)
    {
        $arr = array();

        foreach ($result_query->getResults() as $result) {
            $arr[] = $result->getData();
        }

        return $arr;
    }

    /**
     * Получить JSON из результата поиска
     *
     * @param ResultSet $result_query
     *
     * @return mixed|string
     */
    public function getJSON(ResultSet $result_query = null)
    {
        return json_encode($this->getArray($result_query));
    }

    /**
     * Get format xml
     *
     * @param ResultSet $result_query
     *
     * @return string
     */
    public function getXML(ResultSet $result_query = null)
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->appendChild($dom->createElement("root"));

        foreach ($this->getArray($result_query) as $key => $value) {
            $node = $root->appendChild($dom->createElement("node" . $key));
            foreach ($value as $key_val => $val) {
                $node->appendChild($dom->createElement($key_val, $val));
            }
        }

        return $dom->saveXML();
    }
}