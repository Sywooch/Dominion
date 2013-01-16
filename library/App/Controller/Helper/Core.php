<?php
class App_Controller_Helper_Core extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;

    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */
    public function __construct ()
    {
        // TODO Auto-generated Constructor
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    public function arrayMergeRecursive ($ar1 = array(), $ar2 = array())
    {
        foreach ($ar2 as $key => $val) {
            if (is_array($val)) {
                if (! isset($ar1[$key])) {
                    $ar1[$key] = array();
                }
                $ar1[$key] = $this->arrayMergeRecursive($ar1[$key], $val);
            } else {
                $ar1[$key] = $val;
            }
        }
        return $ar1;
    }
    
}