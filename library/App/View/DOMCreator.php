<?php

//require_once(APPLICATION_PATH . '/../library/App/View/Serializer.php');
require_once(APPLICATION_PATH . '/../library/App/View/DOMxml.class.php');

/**
 * Фабрика генерирующая DOMXml объекты
 */
class App_View_DOMCreator
{

    public function getDOMCreator(DOMDocument $xmlDoc, $xslTemplateFile)
    {
//        $xslDoc = new DOMDocument();

        $imp = new DOMImplementation;
        $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
        $xslDoc = $imp->createDocument("", "", $dtd);
        $xslDoc->resolveExternals = true;
        $xslDoc->substituteEntities = true;
        $xslDoc->encoding = DOMXML_ENCODING_DEFAULT;
        $xslDoc->version = '1.0';
        $xslDoc->standalone = false;

        $xslDoc->load($xslTemplateFile);


//        $xmlDoc = $this->toXml($xml);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        try {
            $transformXHTMLcontent = $proc->transformToXML($xmlDoc);
            return $transformXHTMLcontent;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return false;
        }
    }

    public function getDomObject($xml)
    {
        $imp = new DOMImplementation;
        $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
        $xmlDoc = $imp->createDocument("", "", $dtd);
        $xmlDoc->resolveExternals = true;
        $xmlDoc->substituteEntities = true;
        $xmlDoc->encoding = DOMXML_ENCODING_DEFAULT;
        $xmlDoc->version = '1.0';
        $xmlDoc->standalone = false;

        $xmlDoc->load($xml);
        return $xmlDoc;
    }

//    private function toXml()
//    {
//        $xml_str = $this->serializer->getDOMxml();
////      $xml_str = $this->serializer->getXMLobject();
//        return $xml_str;
//    }
}