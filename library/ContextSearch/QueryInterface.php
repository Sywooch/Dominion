<?php

namespace library\ContextSearch;

/**
 * Class QueryInterface
 *
 * @author  Константин
 *
 * @package lib\ContextSearch
 */
interface QueryInterface
{

    /**
     * Set query to search
     *
     * @param FormatQuery $format_query
     *
     * @return mixed
     */
    public function execQuery(FormatQuery $format_query);

    /*Конвертировать в JSON*/
    public function convertToJSON();

    /*Конвертировать в массив*/
    public function convertToArray();

    /*Конвертировать в XML*/
    public function convertToXml();

}

