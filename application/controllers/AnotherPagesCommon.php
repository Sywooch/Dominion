<?php

class AnotherPagesCommon
{
    public $AnotherPages;
    public $domXml;

    public function __construct()
    {
        $this->AnotherPages = new models_AnotherPages();
    }

    public function getErrorPageText()
    {
        $doc_id = $this->AnotherPages->getDocId('error/404');
        $this->getDocInfo($doc_id);

    }

    public function _setDomXml($domXml)
    {
        $this->domXml = $domXml;
    }

    public function _getDomXml()
    {
        return $this->domXml;
    }

    private function getDocXml($id = 0, $type = 0, $tag = false)
    {
        $doc = $this->AnotherPages->getDocXml($id, $type);
        $doc = stripslashes($doc);
        if (!empty($doc)) {
            if ($tag) {
                $txt = "<?xml version=\"1.0\" encoding=\"{$this->domXml->get_encoding()}\"?><!DOCTYPE stylesheet SYSTEM \"symbols.ent\"><txt>" . $doc . "</txt>";
            } else {
                $txt = "<?xml version=\"1.0\" encoding=\"{$this->domXml->get_encoding()}\"?><!DOCTYPE stylesheet SYSTEM \"symbols.ent\">" . $doc;
            }
            $this->domXml->import_node($txt, false);
        }
    }

    public function getDocInfo($id)
    {
        $info = $this->AnotherPages->getDocInfo($id); //print_r($info);
        if ($info) {
            if (empty($info['TITLE'])) $info['TITLE'] = $info['NAME'];
            if (empty($info['KEYWORDS'])) $info['KEYWORDS'] = $info['NAME'];
            if (empty($info['DESCRIPTION'])) $info['DESCRIPTION'] = $info['NAME'];

            $this->domXml->create_element('docinfo', '', 2);
            $this->domXml->create_element('another_pages_id', $info['ANOTHER_PAGES_ID']);
            $this->domXml->create_element('parent_id', $info['PARENT_ID']);
            $this->domXml->create_element('name', $info['NAME']);
            $this->domXml->create_element('title', $info['TITLE']);
            $this->domXml->create_element('description', $info['DESCRIPTION']);
            $this->domXml->create_element('keywords', $info['KEYWORDS']);


            $this->domXml->go_to_parent();
            $this->getDocXml($info['ANOTHER_PAGES_ID'], 0, true);
        }
    }

}