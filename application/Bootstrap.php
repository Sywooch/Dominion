<?php
/**
 * Base bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function __construct($application)
    {
        parent::__construct($application);
    }

    public function run()
    {
        parent::run();
    }

    public function _initHelper()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Model_ModelLoader()
        );

        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Helper_HelperLoader()
        );
    }


    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);

        return $config;
    }

    protected function _initConfiguration()
    {
        $options = $this->getApplication()->getOptions();

        return $options;
    }


    protected function _initAutoload()
    {
        $loader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));

        return $loader;
    }

    protected function _initSession()
    {
        // should probably only do this for modules other than API? -- GAW
        $session = new Zend_Session_Namespace('frontenf', true);

        return $session;
    }

    /**
     * Bootsturp routing for ЧПУ URL
     *
     * @return type
     */
    public function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $rout = new App_Controller_Router_Bootstrap($front);
        $rout->setRouting();

        return $rout->getRouter();
    }


    protected function _initView()
    {
        $view = new App_View_Xslt;

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewSuffix('xsl');
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        return $view;
    }

}