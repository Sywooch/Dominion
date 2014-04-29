<?php
/**
 * User: Ruslan
 * Date: 04.09.13
 * Time: 12:12
 */

interface BrainPriceImport_ConnectorInterface
{
    /**
     * Get auth SID
     *
     * @return string
     */
    public function getAuthSID();

    /**
     * @return \Buzz\Client\Curl
     */
    public function getCurl();

    /**
     * @return \Buzz\Message\Response|mixed
     */
    public function getResponse();

    /**
     * @return \Buzz\Message\Request
     */
    public function getRequest();
}