<?php

//require_once(APPLICATION_PATH . '/../library/App/View/Serializer.php');
require_once(APPLICATION_PATH . '/../library/App/View/DOMxml.class.php');

class App_View_Xslt extends Zend_View_Abstract
{

    public $serializer;
    private $rootName;

    public function __construct($data = array())
    {
        $this->serializer = new DomXML();
        parent::__construct($data);
    }

    public function setRootName($name)
    {
        $this->rootName = $name;
    }

    protected function _run()
    {
        $template = func_get_arg(0);
        $xmlDoc = $this->toXml();
        $DOMTranform = new App_View_DOMCreator;

//        $g = $DOMTranform->getDOMCreator($xmlDoc, $template);

        $name = '_test.xml';
//      if($_SERVER["HTTP_X_FORWARDED_FOR"]=='193.138.245.146')
//        echo $this->serializer->getXML();
//        exit;
//        $this->serializer->saveXML($name);

        echo $DOMTranform->getDOMCreator($xmlDoc, $template);
        exit;

    }

    private function toXml()
    {
        $xml_str = $this->serializer->getDOMxml();
//      $xml_str = $this->serializer->getXMLobject();
        return $xml_str;
    }

}