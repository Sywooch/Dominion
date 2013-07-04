<?php

class SearchController extends App_Controller_Frontend_Action
{
    public $search;

    public $search_per_page;
    public $query;
    private $config;

    function init()
    {
        parent::init();
        $this->search_per_page = $this->getSettingValue('search_per_page') ? $this->getSettingValue('search_per_page') : 15;

        $this->config = Zend_Registry::get("config");

        if (empty($this->config)) {
            throw new Exception("Error: configuration is not include class: " . __CLASS__ . ", line: " . __LINE__);
        }

        Zend_Search_Lucene_Analysis_Analyzer::setDefault(
            new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
    }

    public function indexAction()
    {
        $request = $this->GetRequest();

        $search_text = $request->getParam("search_text");
        if (!$request->isGet() || empty($search_text)) {

            return;
        }

        $this->createPage($search_text);

        $elasticExecute = $this->_helper->helperLoader("ExecuteElastic");
        $search_engine = $this->config->toArray();

        /** @var $customPaginator Helpers_CustomPaginator */
        $customPaginator = $this->_helper->helperLoader("CustomPaginator");
        $customPaginator->setElements($this->_getParam('page'), $this->search_per_page, $elasticExecute, $search_engine['search_engine'], $search_text);


        /** @var $items ArrayIterator */
        $items = $customPaginator->getCurrentPage()->getArrayCopy();

        $formatData = $elasticExecute->executeFormatData(
            $items,
            $this->currency,
            $this->_helper->helperLoader("Prices_Recount"),
            $this->_helper->helperLoader("Prices_Discount")
        );

        $this->generateXML($customPaginator, $formatData, $search_text);
    }

    /**
     * Generate page
     *
     * @param string $search_text
     */
    private function createPage($search_text)
    {
        $AnotherPages = new models_AnotherPages();

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);

        $doc_id = $AnotherPages->getDocByUrl('/search/');

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setLang($this->lang, $this->lang_id);
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $this->getDocPath($doc_id);

        $search_text = mb_convert_case($search_text, MB_CASE_LOWER, 'UTF-8');
        $this->domXml->create_element('query', $search_text);
        $this->domXml->go_to_parent();
    }

    /**
     * Generate XML
     *
     * @param Helpers_CustomPaginator $paginator
     * @param array $items
     * @param string $searchText
     */
    private function generateXML(Helpers_CustomPaginator $paginator, $items, $searchText)
    {
        $this->domXml->set_tag('//data', true);
        $this->domXml->create_element('search_count', $paginator->getAmount(), 2);
        $this->domXml->go_to_parent();

        $this->openSection($searchText,
            $paginator->getPage(),
            $paginator->getEnd(),
            $paginator->getAmount());

        foreach ($items as $hit) {
            $node_attr = array('item_id' => $hit['ITEM_ID']
            , 'price' => $hit['NEW_PRICE']
            , 'price1' => $hit['PRICE1']
            , 'real_price' => $hit['PRICE']
            , 'real_price1' => $hit['OLD_PRICE']);

            $this->domXml->create_element('search_result', "", 2);
            $this->domXml->set_attribute($node_attr);

            $this->domXml->create_element('href', $hit['URL']);
            $this->domXml->create_element('name', $hit['TYPENAME'] . " " . $hit['BRAND'] . ' ' . $hit['NAME_PRODUCT']);
            $this->domXml->create_element('short_description', $hit['DESCRIPTION']);
            $this->domXml->create_element('sname', $hit['UNIT']);
            $this->domXml->create_element('nat_sname', $hit['SNAME']);

            if (!empty($hit['IMAGE2']) && strchr($hit['IMAGE2'], "#")) {
                $tmp = explode('#', $hit['IMAGE2']);
                $this->domXml->create_element('image_middle', '', 2);
                $this->domXml->set_attribute(array('src' => $tmp[0],
                        'w' => $tmp[1],
                        'h' => $tmp[2]
                    )
                );
                $this->domXml->go_to_parent();
            }

            $this->domXml->go_to_parent();
        }
    }

    private function openSection($query = '', $page, $end = 0, $amount = 0)
    {
        $this->domXml->create_element('section', "", 2);
        $this->domXml->set_attribute(array('query' => $query
        , 'page' => $page
        , 'pcount' => $end
        , 'count' => $amount
        ));

        $this->domXml->go_to_parent();
    }

    private function createIndex()
    {
        $Item = new models_Item();
        $Catalogue = new models_Catalogue();

        set_time_limit(0);
        //удаляем существующий индекс, в большинстве случае эта операция с последующий созданием нового индекса работает гораздо быстрее
        $this->recursive_remove_directory(INDEX_PATH, TRUE);

        try {
            $index = Zend_Search_Lucene::create(INDEX_PATH);
        } catch (Zend_Search_Lucene_Exception $e) {
            echo "<p class=\"ui-bad-message\">Не удалось создать поисковой индекс: {$e->getMessage()}</p>";
        }

        try {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(
                new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
            $i = 0;
            $items = $Item->getItemsSearch();
            foreach ($items as $item) {
                $doc = new Zend_Search_Lucene_Document();
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $item['ITEM_ID']));
                $doc->addField(Zend_Search_Lucene_Field::Text('name', $item['NAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('brand_name', $item['BRAND_NAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('catalogue', $item['CNAME'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Text('description', $item['DESCRIPTION'], 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::Keyword('article', $item['ARTICLE']));

                $attrValues = $Item->getItemSearchAttrs($item['ITEM_ID']);
                if (!empty($attrValues)) {
                    $attrSearchField = '';
                    foreach ($attrValues as $val) {
                        $attrSearchField .= $val['NAME'] . " ";
                    }
                    $doc->addField(Zend_Search_Lucene_Field::Text('attr_val', $attrSearchField, 'UTF-8'));
                }

                $url = $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $item['IMAGE1']));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/it/'));
                $index->addDocument($doc);
                $i++;
            }
            $cats = $Catalogue->getIndexTree();
            foreach ($cats as $cat) {
                $doc = new Zend_Search_Lucene_Document();
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id', $cat['CATALOGUE_ID']));
                $doc->addField(Zend_Search_Lucene_Field::Text('name', $cat['NAME'], 'UTF-8'));

                $url = $this->getRealURL($cat);
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $cat['IMAGE1']));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image_src', '/images/cat/'));
                $index->addDocument($doc);
                $i++;
            }

        } catch (Zend_Search_Lucene_Exception $e) {
            echo "<p class=\"ui-bad-message\">Ошибки индексации: {$e->getMessage()}</p>";
        }

        $index->optimize();
    }

    function recursive_remove_directory($directory, $empty = FALSE)
    {
        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }
        if (!file_exists($directory) || !is_dir($directory)) {
            return FALSE;
        } elseif (is_readable($directory)) {
            $handle = opendir($directory);
            while (FALSE !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    $path = $directory . '/' . $item;
                    if (is_dir($path)) {
                        self::recursive_remove_directory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($handle);
            if ($empty == FALSE) {
                if (!rmdir($directory)) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    public function updateAction()
    {
        set_time_limit(3600);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->createIndex();
    }
}