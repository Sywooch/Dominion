<?php

namespace library\ContextSearch;

/**
 * Class Query
 *
 * @package lib\ContextSearch
 */
class Query implements QueryInterface
{

    /**
     * Result object for search engine
     *
     * @var object
     */
    private $result_query;

    /**
     * Concrete search engine
     *
     * @var object
     */
    private $search_engine;

    /**
     * name Method
     */
    const NAME_METHOD = 'get';

    /**
     * Выбор бизнес логики и передача данных формата запроса
     *
     * @param FormatQuery $format_query
     *
     * @return mixed
     * @throws \Exception
     */
    public function execQuery(FormatQuery $format_query)
    {
        $business_logic = $this->getNameBusinessLogic($format_query->getSearchEngine());
        $search_engine_factory = $format_query->getSearchEngineFactory();

        if (!method_exists($search_engine_factory, $business_logic))
            throw new \Exception("Exception: The methods" . $business_logic . " does not exist");

        $this->search_engine = $search_engine_factory->$business_logic();
        $this->result_query = $this->search_engine->exec($format_query);

        return $this->result_query;
    }

    /**
     * Построение имени бизнес логики
     *
     * @param array $search_engine
     *
     * @return string
     * @throws \Exception
     */
    private function getNameBusinessLogic($search_engine)
    {
        if (empty($search_engine))
            throw new \Exception("Excepton: The parameter search_engine is null");

        $search_engine = explode("_", strtolower($search_engine));

        foreach ($search_engine as $key => $value) {
            $search_array[] = ucfirst($value);
        }

        return self::NAME_METHOD . implode("", $search_array);
    }

    /**
     * Конвертировать результат поиска в массив
     *
     * @return type array
     *
     * @throws \Exception
     */
    public function convertToArray()
    {
        if (empty($this->result_query))
            throw new \Exception("Exception:The variable 'result_query' is null");

        return $this->search_engine->getArray($this->result_query);
    }

    /**
     * Конвертировать результат поиска в JSON формат
     *
     * @return mixed
     * @throws \Exception
     */
    public function convertToJSON()
    {
        if (empty($this->result_query))
            throw new \Exception("Exception:The variable 'result_query' is null");

        return $this->search_engine->getJSON($this->result_query);
    }

    /**
     * Конвертировать рузультат поиска в XML формат
     *
     * @return mixed
     * @throws \Exception
     */
    public function convertToXml()
    {
        if (empty($this->result_query))
            throw new \Exception("Exception:The variable 'result_query' is null");

        return $this->search_engine->getXML($this->result_query);
    }

}
