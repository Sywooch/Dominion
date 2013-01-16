<?php

class App_Auth_Authentication implements Zend_Auth_Adapter_Interface {

    private $_username;
    private $_password;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
    }

    public function getIdentity() {
        return $this->_username;
    }

    private function validatePassword($plain, $encrypted) {
        if (!is_null($plain) && !is_null($encrypted)) {
            if (md5($plain) == $encrypted) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $queryResult = false;
        if ($this->_username && $this->_password) {
          
          $query = new models_Registration();;
          
//          require_once (APPLICATION_PATH . '/modules/admin/models/Users.php');
//          $query = new Admin_Models_Users();
          $queryResult = $query->sigin($this->_username, $this->_password);            
        }
        
        if (!$queryResult) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                    array('user_name' => $this->_username),
                    array('Login failed'));
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
                    array('user_name' => $queryResult['NAME'],
                          'user_id' => $queryResult['USER_ID'],
                          'user_phone' => $queryResult['TELMOB'],
                          'user_email' => $queryResult['EMAIL'],
                    array('Login succesful')));
        }
    }

}