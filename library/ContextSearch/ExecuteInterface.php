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
     * @param library_ContextSearch_FormatQuery $format_query
     *
     * @return mixed
     */
    public function exec(ContextSearch_FormatQuery $format_query);

    /**Получить Json формат из обьекта результата поиска
     *
     * @param \Elastica_ResultSet $result_query
     */
    public function getJSON(ResultSet $result_query);

    /**Получить массив из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getArray(ResultSet $result_query);

    /**Получить XML формат из обьекта результата поиска
     *
     * @param type $result_query
     */
    public function getXML(ResultSet $result_query);
}
