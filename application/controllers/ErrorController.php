<?php

//  require_once 'Zend/Controller/Action.php';
/**
 * Контроллер который обрабатыевает не существующие УРЛ
 */
class ErrorController extends App_Controller_Frontend_Action {

    public function init() {
        parent::init();
    }

    public function errorAction() {


        $errors = $this->_getParam('error_handler');


        // TODO: Пока это гвоздь - в дальнейшем надо сделать логирование ошибок
        if (DEBUG_MODE) {
            echo '<pre>';
            var_dump($errors->exception);
            exit;
        }



        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = $this->view->translate('Page not found');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $this->view->translate('Application error');
                break;
        }

        // log the error to the appropriate logging destinations as defined by the config

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->view404();
        $this->domXml = $ap_helper->getDomXml();

        $this->getResponse()->sendHeaders();
    }

}