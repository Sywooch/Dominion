<?php
use Elastica\ResultSet;

/**
 * Interface for describe function class
 *
 * Class library_ContextSearch_ExecuteInterface
 */
interface ContextSearch_ExecuteInterface
{

    /**
     * Выполнить запрос
     *
     * @return mixed
     */
    public function exec(ContextSearch_FormatQuery $format_query);

    /**
     * Получить Json формат из обьекта результата поиска
     *
     * @return mixed
     */
    public function getJSON(ResultSet $resultQuery);

    /**Получить массив из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getArray(ResultSet $resultQuery);

    /**Получить XML формат из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getXML(ResultSet $resultQuery);
}
