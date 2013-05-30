<?php

namespace library\ContextSearch;
use library\ContextSearch\FormatQuery;

interface ExecuteInterface
{

    /**
     * Выполнить запрос
     *
     * @param FormatQuery $format_query
     *
     * @return mixed
     */
    public function exec(FormatQuery $format_query);

    /**Получить Json формат из обьекта результата поиска
     *
     * @param \Elastica_ResultSet $result_query
     */
    public function getJSON($result_query);

    /**Получить массив из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getArray($result_query);

    /**Получить XML формат из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getXML($result_query);
}
