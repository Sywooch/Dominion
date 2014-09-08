<?php
class Helpers_AnotherPages extends App_Controller_Helper_HelperAbstract
{
    /**
     * Метод вывода меню сайта
     */
    public function makeMenu($parentID = 0, $pathIDs = 0)
    {
        $menu = $this->work_model->getTree($parentID, $this->lang_id);
        if (!empty($menu)) {
            foreach ($menu as $view) {
                if ($view['ANOTHER_PAGES_ID'] == $pathIDs) $on_path = 1;
                else $on_path = 0;

                $this->domXml->create_element('main_menu', '', 2);
                $this->domXml->set_attribute(array('another_pages_id' => $view['ANOTHER_PAGES_ID']
                , 'parent_id' => $view['PARENT_ID']
                , 'on_path' => $on_path,
                    "show_near_catalogue_menu" => $view["SHOW_NEAR_CATALOGUE_MENU"]
                ));

                $href = '';
                if (!empty($view['URL']) && (strpos('http://', $view['url']) !== false)) {
                    $href = $view['URL'];
                } elseif (!empty($view['URL']) && (strpos('http://', $view['url']) === false)) {
                    $href = $this->lang . $view['URL'];
                } else $href = $this->lang . '/doc' . $view['REALCATNAME'];

                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('href', $href);

                $this->makeMenu($view['ANOTHER_PAGES_ID'], $pathIDs);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getTree($parentID = 0)
    {
        $menu = $this->work_model->getTree($parentID, $this->lang_id);
        if (!empty($menu)) {
            foreach ($menu as $view) {
                $this->domXml->create_element('tree', '', 2);
                $this->domXml->set_attribute(array('id' => $view['id']
                ));

                $href = '';
                if (!empty($view['url']) && (strpos('http://', $view['url']) !== false)) {
                    $href = $view['url'];
                } elseif (!empty($view['url']) && (strpos('http://', $view['url']) === false)) {
                    $href = $this->lang . $view['url'];
                } else $href = $this->lang . '/doc/' . $view['file_name'] . '.html';

                $this->domXml->create_element('name', $view['name']);
                $this->domXml->create_element('href', $href);

                $menu_child = $this->work_model->getTree($view['id']);
                if (!empty($menu_child)) {
                    $this->getTree($view['id']);
                }

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getDocPath($id)
    {
        $parent = $this->work_model->getPath($id, $this->lang_id);
        if (!empty($parent)) {
            foreach ($parent as $view) {
                if ($view['PARENT_ID'] == 0) continue;
                $this->domXml->create_element('breadcrumbs', '', 2);
                $this->domXml->set_attribute(array('id' => $view['ANOTHER_PAGES_ID']
                , 'parent_id' => $view['PARENT_ID']
                ));

                $href = '';
                if (!empty($view['URL']) && (strpos('http://', $view['url']) !== false)) {
                    $href = $view['URL'];
                } elseif (!empty($view['URL']) && (strpos('http://', $view['url']) === false)) {
                    $href = $this->lang . $view['URL'];
                } else $href = $this->lang . '/doc/' . $view['REALCATNAME'];

                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('url', $href);
                $this->domXml->go_to_parent();
            }
        }
    }

    public function getDocInfo($id)
    {
        $info = $this->work_model->getDocInfo($id, $this->lang_id); //print_r($info);

        if ($info) {
            $this->domXml->create_element('docinfo', '', 2);
            $this->domXml->set_attribute(array('another_pages_id' => $info['ANOTHER_PAGES_ID']
            , 'parent_id' => $info['PARENT_ID']
            ));

            $this->domXml->create_element('name', $info['NAME']);

            $this->domXml->create_element('title', $info['TITLE']);

            $descript = preg_replace("/\"([^\"]*)\"/", "&#171;\\1&#187;", $info['DESCRIPTION']);
            $descript = preg_replace("/\"/", "&#171;", $descript);
            $this->domXml->create_element('description', $descript);

            $keyword = preg_replace("/\"([^\"]*)\"/", "&#171;\\1&#187;", $info['KEYWORDS']);
            $keyword = preg_replace("/\"/", "&#171;", $keyword);
            $this->domXml->create_element('keywords', $keyword);


            $info_text = $this->work_model->getDocXml($id, 0);
            if (!empty($info_text)) {
                $this->setXmlNode($info_text);
            }


            $this->domXml->go_to_parent();

        }
    }

    public function getHeaderBlock()
    {
        $result = $this->work_model->getHeaderBlock($this->lang_id); //print_r($info);
        if (!empty($result)) {
            $now = date('Y-m-d');
            foreach ($result as $view) {
                $this->domXml->create_element('index_block', '', 2);
                $this->domXml->set_attribute(array('id' => $view['id']
                ));

                $href = '';
                if (!empty($view['url']) && (strpos('http://', $view['url']) !== false)) {
                    $href = $view['url'];
                } elseif (!empty($view['url']) && (strpos('http://', $view['url']) === false)) {
                    $href = $this->lang . $view['url'];
                }

                $this->domXml->create_element('name', $view['name']);
                $this->domXml->create_element('href', $href);
                $this->setXmlNode($view['short_description']);

                if (!empty($view['image_name'])) {
                    $this->domXml->create_element('image_name', '', 2);
                    $this->domXml->set_attribute(array('src' => $view['image_name']
                        )
                    );
                    $this->domXml->go_to_parent();
                }


                $this->domXml->go_to_parent();
            }
        }
    }

    public function getPromoBlock()
    {
        $result = $this->work_model->getPromoBlock($this->lang_id); //print_r($info);
        if (!empty($result)) {
            $now = date('Y-m-d');
            foreach ($result as $view) {
                $this->domXml->create_element('promo_block', '', 2);
                $this->domXml->set_attribute(array('id' => $view['id']
                ));

                $href = '';
                if (!empty($view['url']) && (strpos('http://', $view['url']) !== false)) {
                    $href = $view['url'];
                } elseif (!empty($view['url']) && (strpos('http://', $view['url']) === false)) {
                    $href = $this->lang . $view['url'];
                }

                $this->domXml->create_element('name', $view['name']);
                $this->domXml->create_element('href', $href);
                $this->setXmlNode($view['description']);

                if (!empty($view['image_name'])) {
                    $this->domXml->create_element('image_name', '', 2);
                    $this->domXml->set_attribute(array('src' => $view['image_name']
                        )
                    );
                    $this->domXml->go_to_parent();
                }


                $this->domXml->go_to_parent();
            }
        }
    }

    public function view404()
    {
        $AnotherPages = new models_AnotherPages();
        $doc_id = $AnotherPages->getDocByUrl('/error/404/');
        $error_message = $AnotherPages->getDocXml($doc_id, 0);

        $this->setXmlNode($error_message, 'error_message');
    }
}