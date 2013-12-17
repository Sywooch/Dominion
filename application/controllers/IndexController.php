<?php

class IndexController extends App_Controller_Frontend_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $AnotherPages = new models_AnotherPages();
        $News = new models_News();
        $Catalogue = new models_Catalogue();
        $SectionAlign = new models_SectionAlign();

        $news_index_amount = $this->getSettingValue('news_index_amount') ? $this->getSettingValue('news_index_amount') : 2;

        $dco_id = $AnotherPages->getDocByUrl('/');

        $o_data = array('id' => $dco_id
        , 'currency' => $this->currency
        , 'news_count' => $News->getNewsIndexCount($news_index_amount)
        , 'is_start' => 1
        );

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($dco_id);
        $this->domXml = $ap_helper->getDomXml();

        $ns_helper = $this->_helper->helperLoader('News');
        $ns_helper->setModel($News);
        $ns_helper->setDomXml($this->domXml);
        $ns_helper->getLastNews($news_index_amount);
        $this->domXml = $ns_helper->getDomXml();

        $cat_helper = $this->_helper->helperLoader('Catalogue');
        $cat_helper->setModel($Catalogue);
        $cat_helper->setDomXml($this->domXml);
        $cat_helper->getCatTree(0);
        $this->domXml = $cat_helper->getDomXml();

        $bn_helper = $this->_helper->helperLoader('Banners');
        $bn_helper->setModel($SectionAlign);
        $bn_helper->setDomXml($this->domXml);
        $bn_helper->getBanners('banner_right', 15, 17);
        $this->domXml = $bn_helper->getDomXml();
    }
}