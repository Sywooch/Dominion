<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 06.10.13
 * Time: 17:45
 * To change this template use File | Settings | File Templates.
 */

interface ContextSearch_ElasticSearch_FormatInterface
{
    /**
     * Get query
     *
     * @return mixed
     */
    public function getFormatQuery();

    /**
     * Set from
     *
     * @param $from
     * @return mixed
     */
    public function setFrom($from);

    /**
     * get from
     *
     * @return mixed
     */
    public function getFrom();

    /**
     * Setter fro size
     *
     * @param $size
     * @return mixed
     */
    public function setSize($size);

    /**
     * Getter size
     *
     * @return mixed
     */
    public function getSize();
}