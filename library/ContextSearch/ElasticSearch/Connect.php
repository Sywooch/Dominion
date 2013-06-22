<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:24
 * To change this template use File | Settings | File Templates.
 */

/**
 * Object value for connect to elastic
 *
 * Class ContextSerch_ElasticSearch_Connect
 */
class ContextSearch_ElasticSearch_Connect
{
    /**
     * Config array connect
     *
     * @var array
     */
    private $config = array();

    /**
     * Construct for set main connect parameters
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * Setter for index
     *
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->config['index'] = $index;
    }

    /**
     * Setter for type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->config['type'] = $type;
    }

    /**
     * Setter for action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->config['action'] = $action;
    }

    /**
     * Setter for fields
     *
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->config['name_fields'] = $fields;
    }

    /**
     * Getter for index
     *
     * @return string
     */
    public function getIndex()
    {
        return (isset($this->config['index'])) ? $this->config['index'] : null;
    }

    /**
     * Getter for type
     *
     * @return null
     */
    public function getType()
    {
        return (isset($this->config['type'])) ? $this->config['type'] : null;
    }

    /**
     * Getter action
     *
     * @return null
     */
    public function getAction()
    {
        return (isset($this->config['action'])) ? $this->config['action'] : null;
    }

    /**
     * Getter for config parameters connect to Elastic Search
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Getter Feilds
     *
     * @return null
     */
    public function getFields()
    {
        return (isset($this->config['name_fields'])) ? $this->config['name_fields'] : null;
    }
}