<?php

/**
 * Class QueryInterface
 *
 * @author  Константин
 *
 * @package lib\ContextSearch
 */
interface ContextSearch_QueryInterface
{
    /**
     * Set query to search
     *
     * @param FormatQuery $format_query
     *
     * @return mixed
     */
    public function execQuery(ContextSearch_FormatQuery $format_query);

    /*Конвертировать в JSON*/
    public function convertToJSON();

    /*Конвертировать в массив*/
    public function convertToArray();

    /*Конвертировать в XML*/
    public function convertToXml();

}

