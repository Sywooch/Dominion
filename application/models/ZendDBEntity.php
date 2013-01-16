<?php

class ZendDBEntity {

    protected $_db = NULL;

    function __construct() {
        $registry = Zend_Registry::getInstance();

        if (!Zend_Registry::isRegistered('db_connect')) {
            $config = Zend_Registry::get('config');

            $db_config = array(
                'host' => $config->resources->db->params->host,
                'username' => $config->resources->db->params->username,
                'password' => $config->resources->db->params->password,
                'dbname' => $config->resources->db->params->dbname);

            $adapter = $config->resources->db->adapter;

            $this->_db = Zend_Db::factory($adapter, $db_config);

            $registry->set('db_connect', $this->_db);
        }
        else
            $this->_db = $registry->get('db_connect');

        $this->_db->getConnection()->exec("SET character_set_server = utf8");
        $this->_db->getConnection()->exec("SET NAMES utf8");
        $this->_db->getConnection()->exec("SET CHARACTER SET utf8");
        $this->_db->getConnection()->exec("SET character_set_connection = utf8");
        $this->_db->getConnection()->exec('SET OPTION CHARACTER SET utf8');
    }

}

?>