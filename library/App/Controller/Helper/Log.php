<?php

class App_Controller_Helper_Log extends Zend_Controller_Action_Helper_Abstract {

    /*
    * Function add a record to database table 'logs' after system events.
    *
    * @param $lid integer - id of user event happend with
    * @param $name string - name of user event happend with
    * @param $event string - event that happend with user
    * @return boolean
    * $uid - id of user made event
    */
    public function logEvent($lid, $name, $event) {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $uid = $identity['user_id'];
            switch($event) {
                case 'creation':
                    $message = 'User '.$name.' (id: '.$lid.') was created.';
                    break;
                case 'edition':
                    $message = 'User '.$name.' (id: '.$lid.') was modified.';
                    break;
                case 'activation':
                    $message = 'User '.$name.' (id: '.$lid.') was activated.';
                    break;
                case 'blocking':
                    $message = 'User '.$name.' (id: '.$lid.') was blocked.';
                    break;
                case 'aproove':
                    $message = 'User '.$name.' aproove request  (id: '.$lid.').';
                    break;
                case 'decline':
                    $message = 'User '.$name.' decline request  (id: '.$lid.').';
                    break;
                case 'addarticle':
                    $message = 'User '.$name.' add article  (id: '.$lid.').';
                    break;
                case 'editarticle':
                    $message = 'User '.$name.' edit article  (id: '.$lid.').';
                    break;
                case 'addgroup':
                    $message = 'User '.$name.' add group  (id: '.$lid.').';
                    break;
                case 'editgroup':
                    $message = 'User '.$name.' edit group  (id: '.$lid.').';
                    break;
                case 'deletegroup':
                    $message = 'User '.$name.' delete group  (id: '.$lid.').';
                    break;
            }
            $log = new Model_DbCvs_Log;
            $log->addLog($uid, $message);
            return true;
        }
        return false;
    }

    public function direct($lid, $name, $event) {
        return $this->logEvent($lid, $name, $event);
    }
}
