<?php

/**
 * Генерация sitemap.xml
 */
class SiteMap
{

    private $doc;
    private $root;
    private $page_limit = 40000;
    private $siteurl = 'http://7560000.com.ua';
    private $left = 0;
    private $page = 1;
    private $Catalogue;
    private $AnotherPages;
    private $Item;
    private $Article;
    private $News;

    function __construct()
    {
        Zend_Loader::loadClass('models_AnotherPages');
        Zend_Loader::loadClass('models_Catalogue');
        Zend_Loader::loadClass('models_Item');
        Zend_Loader::loadClass('models_Article');
        Zend_Loader::loadClass('models_News');

        $this->AnotherPages = new models_AnotherPages();
        $this->Catalogue = new models_Catalogue();
        $this->Item = new models_Item();
        $this->Article = new models_Article();
        $this->News = new models_News();
    }

    function run()
    {
        $file_name = '';
        $this->createMap();
        $this->createIndex();

        $cnt = 0;

        $cnt+= $this->getCategs();
        $cnt+= $this->getAnPages();
        $cnt+= $this->getNews();
        $cnt+= $this->getArticle();
        $this->getItemsMap();

        $file_name = 'sitemap.xml';
        $this->saveFile($file_name);
    }

    function createMap()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->root = $this->doc->createElement('urlset');
        $this->root = $this->doc->appendChild($this->root);
        $this->root->setAttribute('xmlns',
                                  'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    function createIndex()
    {
        //Индексная страница
        $main = $this->doc->createElement('url');
        $main = $this->root->appendChild($main);

        $main_loc = $this->doc->createElement('loc');
        $main_loc = $main->appendChild($main_loc);
        $loc_val = $this->doc->createTextNode($this->siteurl);
        $loc_val = $main_loc->appendChild($loc_val);

        $main_lastmod = $this->doc->createElement('lastmod');
        $main_lastmod = $main->appendChild($main_lastmod);
        $lastmod_val = $this->doc->createTextNode(date("c"));
        $lastmod_val = $main_lastmod->appendChild($lastmod_val);

        $main_changefreq = $this->doc->createElement('changefreq');
        $main_changefreq = $main->appendChild($main_changefreq);
        $changefreq_val = $this->doc->createTextNode("daily");
        $changefreq_val = $main_changefreq->appendChild($changefreq_val);

        $main_priority = $this->doc->createElement('priority');
        $main_priority = $main->appendChild($main_priority);
        $priority_val = $this->doc->createTextNode('1.0');
        $priority_val = $main_priority->appendChild($priority_val);
    }

    function limit()
    {
        if ($this->left <= 0) {
            $this->page++;
            $this->left = 0;
        }

        $startSelect = $this->page * $this->page_limit;
        $startSelect = $startSelect > $this->left ? 0 : $startSelect;
        $startSelect = $startSelect < 0 ? 0 : $startSelect;

        return $startSelect;
    }

    function getCategs()
    {
        $catalogs = $this->Catalogue->getSiteMapCatTree();
        $cnt = 0;
        if (!empty($catalogs)) {
            $cnt = count($catalogs);
            foreach ($catalogs as $view) {
                $cats = $this->doc->createElement('url');
                $cats = $this->root->appendChild($cats);

                $href = '';
                if (!empty($view['URL'])) {
                    $href = $view['URL'];
                } else {
                    if (!empty($view['REALCATNAME']) && $view['REALCATNAME'] != '/') {
                        $href = $this->siteurl . $view['REALCATNAME'];
                    }
                }

                $cat_loc = $this->doc->createElement('loc');
                $cat_loc = $cats->appendChild($cat_loc);
                $loc_val = $this->doc->createTextNode($href);
                $loc_val = $cat_loc->appendChild($loc_val);

                $cat_lastmod = $this->doc->createElement('lastmod');
                $cat_lastmod = $cats->appendChild($cat_lastmod);
                $lastmod_val = $this->doc->createTextNode(date("c"));
                $lastmod_val = $cat_lastmod->appendChild($lastmod_val);

                $cat_changefreq = $this->doc->createElement('changefreq');
                $cat_changefreq = $cats->appendChild($cat_changefreq);
                $changefreq_val = $this->doc->createTextNode('weekly');
                $changefreq_val = $cat_changefreq->appendChild($changefreq_val);

                $cat_priority = $this->doc->createElement('priority');
                $cat_priority = $cats->appendChild($cat_priority);
                $priority_val = $this->doc->createTextNode('0.5');
                $priority_val = $cat_priority->appendChild($priority_val);
            }
        }

        return $cnt;
    }

    function getAnPages()
    {
        $another_pages = $this->AnotherPages->getSiteMapTree();

        if (!empty($another_pages)) {
            $cnt = count($another_pages);
            foreach ($another_pages as $view) {
                $cats = $this->doc->createElement('url');
                $cats = $this->root->appendChild($cats);

                $href = '';
                if (!empty($view['URL'])) {
                    if (strpos($view['URL'], 'http://') !== false) {
                        $href = $view['URL'];
                    }
                    else
                        $href = $this->siteurl . $view['URL'];
                }
                elseif (!empty($view['REALCATNAME']) && $view['REALCATNAME'] != '/') {
                    $href = $this->siteurl . '/doc' . $view['REALCATNAME'];
                } else {
                    $href = $this->siteurl . '/doc/' . $view['ANOTHER_PAGES_ID'] . '/';
                }

                $cat_loc = $this->doc->createElement('loc');
                $cat_loc = $cats->appendChild($cat_loc);
                $loc_val = $this->doc->createTextNode($href);
                $loc_val = $cat_loc->appendChild($loc_val);

                $cat_lastmod = $this->doc->createElement('lastmod');
                $cat_lastmod = $cats->appendChild($cat_lastmod);
                $lastmod_val = $this->doc->createTextNode(date("c"));
                $lastmod_val = $cat_lastmod->appendChild($lastmod_val);

                $cat_changefreq = $this->doc->createElement('changefreq');
                $cat_changefreq = $cats->appendChild($cat_changefreq);
                $changefreq_val = $this->doc->createTextNode('weekly');
                $changefreq_val = $cat_changefreq->appendChild($changefreq_val);

                $cat_priority = $this->doc->createElement('priority');
                $cat_priority = $cats->appendChild($cat_priority);
                $priority_val = $this->doc->createTextNode('0.5');
                $priority_val = $cat_priority->appendChild($priority_val);
            }
        }

        return $cnt;
    }

    function getNews()
    {
        $news = $this->News->getSiteMapNews();
        $cnt = count($news);
        if (!empty($news)) {
            foreach ($news as $view) {
                $cats = $this->doc->createElement('url');
                $cats = $this->root->appendChild($cats);

                $href = $this->siteurl . '/news/' . $view . '/';

                $cat_loc = $this->doc->createElement('loc');
                $cat_loc = $cats->appendChild($cat_loc);
                $loc_val = $this->doc->createTextNode($href);
                $loc_val = $cat_loc->appendChild($loc_val);

                $cat_lastmod = $this->doc->createElement('lastmod');
                $cat_lastmod = $cats->appendChild($cat_lastmod);
                $lastmod_val = $this->doc->createTextNode(date("c"));
                $lastmod_val = $cat_lastmod->appendChild($lastmod_val);

                $cat_changefreq = $this->doc->createElement('changefreq');
                $cat_changefreq = $cats->appendChild($cat_changefreq);
                $changefreq_val = $this->doc->createTextNode('weekly');
                $changefreq_val = $cat_changefreq->appendChild($changefreq_val);

                $cat_priority = $this->doc->createElement('priority');
                $cat_priority = $cats->appendChild($cat_priority);
                $priority_val = $this->doc->createTextNode('0.5');
                $priority_val = $cat_priority->appendChild($priority_val);
            }
        }

        return $cnt;
    }

    function getArticle()
    {
        $article = $this->Article->getSiteMapArticle();
        $cnt = count($article);
        if (!empty($article)) {
            foreach ($article as $view) {
                $cats = $this->doc->createElement('url');
                $cats = $this->root->appendChild($cats);

                $href = $this->siteurl . '/article/' . $view . '/';

                $cat_loc = $this->doc->createElement('loc');
                $cat_loc = $cats->appendChild($cat_loc);
                $loc_val = $this->doc->createTextNode($href);
                $loc_val = $cat_loc->appendChild($loc_val);

                $cat_lastmod = $this->doc->createElement('lastmod');
                $cat_lastmod = $cats->appendChild($cat_lastmod);
                $lastmod_val = $this->doc->createTextNode(date("c"));
                $lastmod_val = $cat_lastmod->appendChild($lastmod_val);

                $cat_changefreq = $this->doc->createElement('changefreq');
                $cat_changefreq = $cats->appendChild($cat_changefreq);
                $changefreq_val = $this->doc->createTextNode('weekly');
                $changefreq_val = $cat_changefreq->appendChild($changefreq_val);

                $cat_priority = $this->doc->createElement('priority');
                $cat_priority = $cats->appendChild($cat_priority);
                $priority_val = $this->doc->createTextNode('0.5');
                $priority_val = $cat_priority->appendChild($priority_val);
            }
        }

        return $cnt;
    }

    function getItemsMap()
    {
        $items = $this->Item->getSiteMapItems();
        if (!empty($items)) {
            $cnt = 0;
            foreach ($items as $view) {
                $cats = $this->doc->createElement('url');
                $cats = $this->root->appendChild($cats);

                $href = $this->siteurl . $view['CATALOGUE_REALCATNAME'] . $view['ITEM_ID'] . '-' . $view['CATNAME'] . '/';

                $cat_loc = $this->doc->createElement('loc');
                $cat_loc = $cats->appendChild($cat_loc);
                $loc_val = $this->doc->createTextNode($href);
                $loc_val = $cat_loc->appendChild($loc_val);

                $cat_lastmod = $this->doc->createElement('lastmod');
                $cat_lastmod = $cats->appendChild($cat_lastmod);
                $lastmod_val = $this->doc->createTextNode(date("c"));
                $lastmod_val = $cat_lastmod->appendChild($lastmod_val);

                $cat_changefreq = $this->doc->createElement('changefreq');
                $cat_changefreq = $cats->appendChild($cat_changefreq);
                $changefreq_val = $this->doc->createTextNode('weekly');
                $changefreq_val = $cat_changefreq->appendChild($changefreq_val);

                $cat_priority = $this->doc->createElement('priority');
                $cat_priority = $cats->appendChild($cat_priority);
                $priority_val = $this->doc->createTextNode('0.5');
                $priority_val = $cat_priority->appendChild($priority_val);

                $cnt++;
            }
        }
    }

    function saveFile($file_name)
    {
//        $dir_path = SITE_PATH . '/' . $file_name;

        if (is_file(SITE_PATH . '/' . $file_name)) {
            unlink(SITE_PATH . '/' . $file_name);
        }

        $xml = isset($this->doc) ? $this->doc->saveXML() : '';

        if (!empty($xml)) {
            $handle = fopen(SITE_PATH . '/' . $file_name, 'a');
            fwrite($handle, $xml);
            fclose($handle);
            chmod(SITE_PATH . '/' . $file_name, 0644);
        }
    }

}