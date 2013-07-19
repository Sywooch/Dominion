<?php

class App_Controller_Router_Bootstrap
{

    private $_router;
    private $_front;
    private $_is301 = false;

    public function __construct(Zend_Controller_Front $front)
    {
        $this->_front = $front;
        $this->_router = $front->getRouter();
    }

    public function setRouting()
    {
        $this->_initRedirector();

        $this->_initOldCatUrl();
        $this->_initOldItemUrl();

        $this->_initSefUrlAliasingCat();

        $this->_initContorollerDefault();
        $this->_initPagesDefault();
        $this->_initAliasingAjax();
        $this->_initAliasingDoc();
        $this->_initAliasingNews();
        $this->_initAliasingCompare();
        $this->_initAliasingArticle();
        $this->_initAliasingItem();
        $this->_initAliasingCat();
        $this->_initAliasingCatAttrib();
        $this->_initAliasingSearch();

        $this->_initAliasingSitemap();

        $this->_initAliasingRegister();

        if ($this->_is301) {
            return;
        }
    }

    public function getFront()
    {
        return $this->_front;
    }

    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Инициализация редиректа - если УРЛ который получили требует 301 редиректа
     */
    public function _initRedirector()
    {
        $AnotherPages = new models_AnotherPages();

        $req = new Zend_Controller_Request_Http();
        $uri = $req->getRequestUri();

        // избавимся от хвостов урла - чтобы можно было редиректить на ссылки с метками utm

        $pattern = "/(^.*\/).*$/uis";

        if (preg_match($pattern, $uri, $matches)) {
            $uri = $matches[1];
        }

        $url_to = $AnotherPages->getRedirector($uri);

        if (!empty($url_to)) {
            $href = 'http://' . $_SERVER['HTTP_HOST'] . $url_to;

            $response = new Zend_Controller_Response_Http();
            $response->setRedirect($href, 301);
            $response->sendHeaders();
            exit;
        }
    }

    public function _initSefUrlAliasingCat()
    {

        $AnotherPages = new models_AnotherPages();
        $req = new Zend_Controller_Request_Http();
        $uri = $req->getRequestUri();

        $paramsUrl = '';
//
//        $uri = "/vse-dlya-doma-dachi-i-sada/motobloki-i-kultivatory/brigadier/";
//        $uri = "/vse-dlya-doma-dachi-i-sada/motobloki-i-kultivatory/brigadier/page/2/br/bsdf/pmin/1234/";

//        $pattern_page = '/(.*)(\/(?:br|page|ar|at|pmin|pmax).+?)/Uis';

//        $pattern_page = '/(.*)(\/((br\/(b)*)|(page|ar|at|pmin|pmax)).+?)/Uis';
//        $pattern_page = '^/(.*)\/(br\/(b\d+)*?)?/Uis';
//        $pattern_page = '/^(.*)\/((br\/(b\d+)+\/)|page)?$/Uis';
        $pattern_page = '/^(.*)((?:\/br\/|\/page\/|\/at\/|\/ar\/|\/pmin\/|\/pmax\/).*)?$/Uis';

        if (preg_match($pattern_page, $uri, $out)) {

            $uri = $out[1];
            if (!empty($out[2])) {

                // Отеразем первый слэш - надо для того чтобы потом корректно его соединить
                $g = substr($out[2], 0, 1);
                if ('/' === substr($out[2], 0, 1)) {
                    $paramsUrl = substr($out[2], 1, strlen($out[2]) - 1);
                } else {
                    $paramsUrl = $out[2];
                }

            }
        }

        $siteURLbySEFU = $AnotherPages->getSiteURLbySEFU($uri);

        if (!empty($siteURLbySEFU)) {
            $req->setRequestUri($siteURLbySEFU . $paramsUrl);
        }

        $front = Zend_Controller_Front::getInstance();
        $front->setRequest($req);
    }

    private function _initAliasingRegister()
    {
        $this->_router->addRoute(
            'register',
            new Zend_Controller_Router_Route_Regex(
                'register/(\w*)\.html',
                array(
                    'controller' => 'register'
                ),
                array(
                    1 => 'action',
                )
            )
        );

        $this->_router->addRoute(
            'registerall',
            new Zend_Controller_Router_Route_Regex(
                'register\.html',
                array(
                    'controller' => 'register',
                    'action' => 'index'
                )
            )
        );
    }

    private function _initAliasingAjax()
    {
        $this->_router->addRoute(
            'ajax',
            new Zend_Controller_Router_Route_Regex(
                'ajax/(\w*)',
                array(
                    'controller' => 'ajax'
                ),
                array(
                    1 => 'action',
                )
            )
        );
    }

    private function _initAliasingItem()
    {
        $routed = new Zend_Controller_Router_Route_Regex(
            '.*/(\d*)-.*?(/([^/].*?))?',
            array(
                'controller' => 'item',
                'action' => 'view'
            ),
            array(
                1 => 'id',
                3 => 'section'
            )
        );

        $this->_router->addRoute('item', $routed);

//    $values3 = $routed->match('/vstraivaemaya-tehnika-dlya-kyhni/vstraivaemie_dyhovie_shkafi/11702-vstraivaemyiy-duhovoiy-shkaf-whirlpool-akp-230-ix/');
//    print_r($values3);
//    exit;
    }

    private function _initAliasingSearch()
    {

        $routed = new Zend_Controller_Router_Route_Regex(
            'search/([^/].*?)(/page/([^/]\d*?))?',
            array(
                'controller' => 'search',
                'action' => 'index'
            ),
            array(
                1 => 'search_text',
                3 => 'page'
            )
        );
        $this->_router->addRoute('search', $routed);

//    $values3 = $routed->match('/search/блендер braun/page/3/');
//    print_r($values3);
//    exit;
    }

    private function _initAliasingCat()
    {
        $this->_router->addRoute(
            'cat',
            new Zend_Controller_Router_Route_Regex(
                'cat/([^/]\d*)',
                array(
                    'controller' => 'cat',
                    'action' => 'index'
                ),
                array(
                    1 => 'id'
                )
            )
        );

        $this->_router->addRoute(
            'cat_pager',
            new Zend_Controller_Router_Route_Regex(
                'cat/([^/]\d*)/page/([^/]\d*)',
                array(
                    'controller' => 'cat',
                    'action' => 'index'
                ),
                array(
                    1 => 'id',
                    2 => 'page'
                )
            )
        );

        $this->_router->addRoute(
            'cat_brand',
            new Zend_Controller_Router_Route_Regex(
                'cat/([^/]\d*)/brand/([^/]\d*)',
                array(
                    'controller' => 'cat',
                    'action' => 'index'
                ),
                array(
                    1 => 'id',
                    2 => 'brand_id'
                )
            )
        );

        $this->_router->addRoute(
            'cat_brand_page',
            new Zend_Controller_Router_Route_Regex(
                'cat/([^/]\d*)/brand/([^/]\d*)/page/([^/]\d*)',
                array(
                    'controller' => 'cat',
                    'action' => 'index'
                ),
                array(
                    1 => 'id',
                    2 => 'brand_id',
                    3 => 'page'
                )
            )
        );
    }

    private function _initAliasingCatAttrib()
    {
        $routed = new Zend_Controller_Router_Route_Regex(
            'cat/([^/]\d*)(/brand/([^/]\d*?))?(/page/([^/]\d*?))?(/br/([^/].*?))?(/at/([^/].*?))?(/ar/([^/].*?))?(/pmin/([^/].*?))?(/pmax/([^/].*?))?',
            array(
                'controller' => 'cat',
                'action' => 'index'
            ),
            array(
                1 => 'id',
                3 => 'brand_id',
                5 => 'page',
                7 => 'br',
                9 => 'at',
                11 => 'ar',
                13 => 'pmin',
                15 => 'pmax'
            )
        );

        $this->_router->addRoute('cat_attribut', $routed);

//    $values3 = $routed->match('/cat/4/br/b37b773/at/a571v6447a510v5884-15196a343v4241-4476/ar/a510v134-701a343v93-424/');
//    var_dump($values3);
//    exit;
    }

    private function _initAliasingSitemap()
    {
        $this->_router->addRoute(
            'sitemap',
            new Zend_Controller_Router_Route_Regex(
                'sitemap\.xml',
                array(
                    'controller' => 'sitemap',
                    'action' => 'index'
                )
            )
        );
    }

    private function _initPagesDefault()
    {
        $this->_router->addRoute(
            'pagesdefault',
            new Zend_Controller_Router_Route_Regex(
                '(\w*)\.html',
                array(
                    'action' => 'index'
                ),
                array(
                    1 => 'controller',
                )
            )
        );

        $this->_router->addRoute(
            'pagesdefault_multilingual',
            new Zend_Controller_Router_Route_Regex(
                '(\w{2})/(\w*)\.html',
                array(
                    'action' => 'index'
                ),
                array(
                    1 => 'lang',
                    2 => 'controller',
                )
            )
        );
    }

    private function _initContorollerDefault()
    {
        $this->_router->addRoute(
            'def',
            new Zend_Controller_Router_Route(
                ':controller',
                array(
                    'controller' => 'index',
                    'action' => 'index'
                )
            )
        );

        $this->_router->addRoute(
            'default_multilingual',
            new Zend_Controller_Router_Route(
                ':lang/:controller/:action/*',
                array(
                    'controller' => 'index',
                    'action' => 'index'
                ),
                array(
                    'lang' => '\w{2}'
                )
            )
        );
    }

    private function _initAliasingDoc()
    {

        $routed = new Zend_Controller_Router_Route_Regex(
            'doc/(.+)',
            array(
                'controller' => 'doc',
                'action' => 'view'
            ),
            array(
                1 => 'n'
            )
        );

        $this->_router->addRoute('doc', $routed);

        $routed_soc = new Zend_Controller_Router_Route_Regex(
            'doc/social',
            array(
                'controller' => 'doc',
                'action' => 'social'
            )
        );

        $this->_router->addRoute('doc_social', $routed_soc);

//    $values3 = $routed_soc->match('/doc/social/?in=facebook&url=item-7371&title=Блендер+Tefal+HB+4071');
//    print_r($values3);
//    exit;
    }

    private function _initAliasingNews()
    {
        $this->_router->addRoute(
            'news',
            new Zend_Controller_Router_Route_Regex(
                'news/(.*)',
                array(
                    'controller' => 'news',
                    'action' => 'view'
                ),
                array(
                    1 => 'n'
                )
            )
        );

        $this->_router->addRoute(
            'newspager',
            new Zend_Controller_Router_Route_Regex(
                'news/page/([^/]\d*)',
                array(
                    'controller' => 'news',
                    'action' => 'all'
                ),
                array(
                    1 => 'page'
                )
            )
        );

        $this->_router->addRoute(
            'all_news',
            new Zend_Controller_Router_Route_Regex(
                'news',
                array(
                    'controller' => 'news',
                    'action' => 'all'
                )
            )
        );
    }

    private function _initAliasingCompare()
    {
        $this->_router->addRoute(
            'compare',
            new Zend_Controller_Router_Route_Regex(
                'compare/([^/]\d*)',
                array(
                    'controller' => 'compare',
                    'action' => 'index'
                ),
                array(
                    1 => 'id'
                )
            )
        );
    }

    private function _initAliasingArticle()
    {
        $this->_router->addRoute(
            'article',
            new Zend_Controller_Router_Route_Regex(
                'article/(.*)',
                array(
                    'controller' => 'article',
                    'action' => 'view'
                ),
                array(
                    1 => 'n'
                )
            )
        );

        $this->_router->addRoute(
            'articlepager',
            new Zend_Controller_Router_Route_Regex(
                'article/page/([^/]\d*)',
                array(
                    'controller' => 'article',
                    'action' => 'all'
                ),
                array(
                    1 => 'page'
                )
            )
        );

        $this->_router->addRoute(
            'all_article',
            new Zend_Controller_Router_Route_Regex(
                'article',
                array(
                    'controller' => 'article',
                    'action' => 'all'
                )
            )
        );
    }

    private function _initOldCatUrl()
    {
//        if ($_GET) {
//            return true;
//        }

        $AnotherPages = new models_AnotherPages();

        if (!empty($_SERVER['PATH_INFO'])) {
            $_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
        }

        if (strlen($_SERVER['REQUEST_URI']) > 1) {
            $urlInfo = parse_url($_SERVER['REQUEST_URI']);

            $sefuByOld = $AnotherPages->getSefURLbyOldURL($urlInfo['path']);

            if (empty($sefuByOld)) {
                return;
            }

            // Если новый и старый URL не одинаковый - отправлем редирект
            // Иначе будет зацикливание!
            if ($sefuByOld != $urlInfo['path']) {
                $href = "http://{$_SERVER['HTTP_HOST']}{$sefuByOld}";

                if (!empty($urlInfo['query'])) {
                    $href .= "?{$urlInfo['query']}";
                }

                $response = new Zend_Controller_Response_Http();
                $response->setRedirect($href, 301);
                $response->sendHeaders();
                exit;
            }
        }
    }

    private function _initOldItemUrl()
    {
        $Item = new models_Item();

        $pattern = '/item\/(\d*)\/.*/';
        if (preg_match($pattern, $_SERVER['REQUEST_URI'], $out)) {
            $item_id = !empty($out[1]) ? $out[1] : 0;
            if (!empty($item_id)) {
                $item = $Item->getItemChpuInfo($item_id);
                if (!empty($item)) {
                    $href = 'http://' . $_SERVER['HTTP_HOST'] . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
                    $response = new Zend_Controller_Response_Http();
                    $response->setRedirect($href, 301);
                    $response->sendHeaders();
                }
            }
        }
    }

    protected function redirect301($path)
    {
        $req = new Zend_Controller_Request_Http();
        // Меняем URL на который нам нужно для вывода ошибки
        $req->setRequestUri('/error/error/');
        $req->setParam('url', $path);
        $errors->type = 301;
        $req->setParam('error_handler', $errors);
        $front = Zend_Controller_Front::getInstance();
        $front->setRequest($req);
        $this->_is301 = true;
    }

}