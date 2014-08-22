<?php
class ArticleController extends App_Controller_Frontend_Action
{
    public $article_id = 0;

    function init()
    {
        parent::init();

        $Article = new models_Article();

        if ($this->work_action != 'all') {
            $file_name = $this->_getParam('n');
            if (!empty($file_name)) {
                $this->article_id = $Article->getArticleId($file_name);
            }

            $res = $Article->getArticleSingle($this->article_id);
            if ($this->article_id == 0 || ($this->article_id > 0 && empty($res))) {
                $this->page_404();
            }
        }
    }

    public function allAction()
    {
        $Article = new models_Article();
        $AnotherPages = new models_AnotherPages();
        $SectionAlign = new models_SectionAlign();

        $article_per_page = $this->getSettingValue('article_per_page') ? $this->getSettingValue('article_per_page') : 15;
        $count = $Article->getArticleCount();

        $page = $this->_getParam('page', 1);

        $doc_id = $AnotherPages->getDocByUrl('/article/');

        $o_data['id'] = $this->article_id;
        $o_data['is_slider'] = 1;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setLang($this->lang, $this->lang_id);
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $startSelect = ($page - 1) * $article_per_page;
        $startSelect = $startSelect > $count ? 0 : $startSelect;
        $startSelect = $startSelect < 0 ? 0 : $startSelect;

        $pcount = ceil($count / $article_per_page);

        $this->makeSectionInfo($count, $page, $pcount);

        $this->domXml->set_tag('//data', true);

        $ns_helper = $this->_helper->helperLoader('Article');
        $ns_helper->setLang($this->lang, $this->lang_id);
        $ns_helper->setModel($Article);
        $ns_helper->setDomXml($this->domXml);
        $ns_helper->getDocPath(0);
        $ns_helper->getArticles($startSelect, $article_per_page);
        $this->domXml = $ns_helper->getDomXml();

        /** @var $catalogueHelper Helpers_Catalogue */
        $catalogueHelper = $this->_helper->helperLoader("Catalogue");
        $catalogueHelper->setDomXml($this->domXml);
        $catalogueHelper->setModel(new models_Catalogue());
        $catalogueHelper->generateCatalogueMenu(0);

        $bn_helper = $this->_helper->helperLoader('Banners');
        $bn_helper->setModel($SectionAlign);
        $bn_helper->setDomXml($this->domXml);
        $bn_helper->getBanners('banner_right', 15, 17);
        $this->domXml = $bn_helper->getDomXml();
    }

    public function viewAction()
    {
        $Article = new models_Article();
        $SectionAlign = new models_SectionAlign();

        $o_data['id'] = $this->article_id;
        $o_data['is_slider'] = 1;

        $this->openData($o_data);

        $ns_helper = $this->_helper->helperLoader('Article');
        $ns_helper->setLang($this->lang, $this->lang_id);
        $ns_helper->setModel($Article);
        $ns_helper->setDomXml($this->domXml);
        $ns_helper->getDocPath($this->article_id);
        $ns_helper->getArticleSingle($this->article_id);
        $ns_helper->getMetaSingle($this->article_id);
        $this->domXml = $ns_helper->getDomXml();

        $bn_helper = $this->_helper->helperLoader('Banners');
        $bn_helper->setModel($SectionAlign);
        $bn_helper->setDomXml($this->domXml);
        $bn_helper->getBanners('banner_right', 15, 17);
        $this->domXml = $bn_helper->getDomXml();
    }


    public function makeSectionInfo($count, $page, $pcount)
    {
        $this->domXml->set_tag('//page/data', true);
        $this->domXml->create_element('section', '', 2);

        $this->domXml->set_attribute(array('count' => $count
        , 'page' => $page
        , 'pcount' => $pcount
        ));

        $this->domXml->go_to_parent();
    }

}