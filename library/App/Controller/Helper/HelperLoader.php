<?php
class App_Controller_Helper_HelperLoader extends Zend_Controller_Action_Helper_Abstract {
  /**
   * @var Zend_Loader_PluginLoader
   */
  public $pluginLoader;

  /**
   * Конструктор: инициализирует плагин загрузки
   * 
   * @return void
   */
  public function __construct()
  {
      $this->pluginLoader = new Zend_Loader_PluginLoader();
  }

  /**
   * Загружает форму с выбранными опциями
   * 
   * @param  string $name 
   * @param  array|Zend_Config $options 
   * @return Zend_Form
   */
  public function loadHelper($name, $options = null)
  {
      $module  = $this->getRequest()->getModuleName();
      $front   = $this->getFrontController();
      $default = $front->getDispatcher()
                       ->getDefaultModule();

      $moduleDirectory = $front->getControllerDirectory($module);      
      $formsDirectory  = dirname($moduleDirectory) . '/helpers';
      
      $prefix = (('default' == $module) ? '' : ucfirst($module) . '_')
               . 'Helpers_';
      
      $this->pluginLoader->addPrefixPath($prefix, $formsDirectory);

      $name      = ucfirst((string) $name);
      
      $formClass = $this->pluginLoader->load($name);
      
      return new $formClass($options);
  }

  /**
   * Паттерн Стратегии: вызываем помощник как метод брокера
   * 
   * @param  string $name 
   * @param  array|Zend_Config $options 
   * @return Zend_Form
   */
  public function direct($name, $options = null){
      return $this->loadHelper($name, $options);
  }
}
?>