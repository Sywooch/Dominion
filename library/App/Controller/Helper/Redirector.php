<?php

class App_Controller_Helper_Redirector extends Zend_Controller_Action_Helper_Redirector {

    public function direct($action, $controller = null, $module = null, array $params = array()) {
        print_r($action);die();
        if (sizeof($params) == 0) {
            $params[""] = "";
        }
        $this->gotoSimple($action, $controller, $module, $params);
    }

    public function setGotoSimple($action, $controller = null, $module = null, array $params = array()) {
        $dispatcher = $this->getFrontController()->getDispatcher();
        $request = $this->getRequest();
        $curModule = $request->getModuleName();
        $useDefaultController = false;

        if (null === $controller && null !== $module) {
            $useDefaultController = true;
        }

        if (null === $module) {
            $module = $curModule;
        }

        if ($module == 'frontend') {
            $module = '';
        } else {
            $module = $dispatcher->getDefaultModule();
        }

        if (null === $controller && !$useDefaultController) {
            $controller = $request->getControllerName();
            if (empty($controller)) {
                $controller = $dispatcher->getDefaultControllerName();
            }
        }

        $params['module'] = $module;
        $params['controller'] = $controller;
        $params['action'] = $action;

        $router = $this->getFrontController()->getRouter();

        $url = $router->assemble($params, 'default', true);

        $this->_redirect($url);
    }

}
