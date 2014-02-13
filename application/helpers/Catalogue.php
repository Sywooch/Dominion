<?php

class Helpers_Catalogue extends App_Controller_Helper_HelperAbstract
{

    public function getCatTree($parentId = 0)
    {
        $Item = new models_Item();

        $pathIDs = array();

        $catalog_id = 0;
        if ($this->params['work_controller'] == 'cat') {
            $catalog_id = $this->params['input_id'];
            $pathIDs = $this->work_model->getAllParents($this->params['input_id'], $pathIDs);
            $pathIDs[count($pathIDs)] = $catalog_id;
        } elseif ($this->params['work_controller'] == 'item') {
            $catalog_id = $Item->getItemCatalog($this->params['input_id']);
            if (!empty($catalog_id)) {
                $pathIDs = $this->work_model->getAllParents($catalog_id, $pathIDs);
                $pathIDs[count($pathIDs)] = $catalog_id;
            }
        }
        $cats = $this->work_model->getTree($parentId, $this->lang_id);

        if (!empty($cats)) {
            foreach ($cats as $cat) {
                $select = 0;
                if (in_array($cat['CATALOGUE_ID'], $pathIDs)) {
                    $select = 1;
                }

                $this->domXml->create_element('cattree', '', 2);
                $this->domXml->set_attribute(array('catalogue_id' => $cat['CATALOGUE_ID']
                , 'parent_id' => $cat['PARENT_ID']
                , 'is_index' => $cat['IS_INDEX']
                , 'on_path' => $select
                ));


//        $href = $this->lang.'/cat/'.$cat['id'].'-'.$cat['file_name'].'.html';
                $href = $cat['REALCATNAME'];

                $this->domXml->create_element('name', $cat['NAME']);
                $this->domXml->create_element('href', $href);

                if (!empty($cat['IMAGE1']) && strchr($cat['IMAGE1'], "#")) {
                    $tmp = explode('#', $cat['IMAGE1']);
                    $this->domXml->create_element('image', '', 2);
                    $this->domXml->set_attribute(array('src' => $tmp[0],
                            'w' => $tmp[1],
                            'h' => $tmp[2]
                        )
                    );
                    $this->domXml->go_to_parent();
                }

                $this->getCatTree($cat['CATALOGUE_ID']);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getCatSubTree($parentId = 0)
    {
        $cats = $this->work_model->getIndexTree($parentId, $this->lang_id);

        // Если список для подкаталога пуст - выходим
        // Требуется рефакторинг - модульное тестирование никак не провести с такой струтктурой
        if (empty($cats))
            return;


        foreach ($cats as $cat) {

            $this->domXml->create_element('sub_cattree', '', 2);
            $this->domXml->set_attribute(array('catalogue_id' => $cat['CATALOGUE_ID']
            , 'parent_id' => $cat['PARENT_ID']
            ));

            $href = $cat['REALCATNAME'];

            $this->domXml->create_element('name', $cat['NAME']);
            $this->domXml->create_element('href', $href);

            if (!empty($cat['IMAGE1']) && strchr($cat['IMAGE1'], "#")) {
                $tmp = explode('#', $cat['IMAGE1']);
                $this->domXml->create_element('image', '', 2);
                $this->domXml->set_attribute(array('src' => $tmp[0],
                        'w' => $tmp[1],
                        'h' => $tmp[2]
                    )
                );
                $this->domXml->go_to_parent();
            }

            $this->getCatBrands($cat['CATALOGUE_ID']);

            $this->domXml->go_to_parent();
        }
    }

    private function getCatBrands($id)
    {
        $AnotherPages = new models_AnotherPages();
        $result = $this->work_model->getBrands($id);

        $realcatname = $this->work_model->getCatRealCat($id);

        if (!empty($result)) {
            foreach ($result as $view) {
                $this->domXml->create_element('brand_view', '', 2);
                $this->domXml->set_attribute(array('brand_id' => $view['BRAND_ID']
                ));


                $href = $realcatname . $view['ALT_NAME'] . '/';

                $_href = $AnotherPages->getSefURLbyOldURL($href);
                if (!empty($_href))
                    $href = $_href;

                $this->domXml->create_element('name', $view['NAME']);
                $this->domXml->create_element('href', $href);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getCatInfo($id)
    {
        $catinfo = $this->work_model->getCatInfo($id, $this->lang_id);

        if (!empty($catinfo)) {
            $this->domXml->create_element('docinfo', '', 2);

            $this->domXml->create_element('name', $catinfo['NAME']);

            $this->domXml->create_element('title', $catinfo['TITLE']);
            $this->domXml->create_element('keywords', $catinfo['KEYWORD_META']);
            $this->domXml->create_element('description', $catinfo['DESC_META']);

            $this->setXmlNode($catinfo['LONG_TEXT'], 'long_text');

            $this->domXml->go_to_parent();
        }
    }

    public function getCatalogPath($id, $item_name = '')
    {
        $childs = array();
        $childs[count($childs)] = $id;
        $parent = $id;

        while ($parent > 0) {
            $cat = $this->work_model->getParents($parent, $this->lang_id);
            $parent = $cat['PARENT_ID'];
            if ($parent == 0)
                break;
            $childs[count($childs)] = $cat['PARENT_ID'];
        }

        $this->domXml->create_element('breadcrumbs', '', 2);
        $this->domXml->set_attribute(array('id' => 0,
                'parent_id' => 0
            )
        );
        $href = '/cat/';

        $this->domXml->create_element('name', 'Весь каталог');
        $this->domXml->create_element('url', $href);

        $this->getSubCatalogPath(0, 0);

        $this->domXml->go_to_parent();

        if (!empty($childs)) {
            $childs = array_reverse($childs);
            foreach ($childs as $key => $view) {
                $parent = $this->work_model->getParents($view, $this->lang_id);
                if (!empty($parent)) {
                    $this->domXml->create_element('breadcrumbs', '', 2);
                    $this->domXml->set_attribute(array('id' => $parent['CATALOGUE_ID'],
                            'parent_id' => $parent['PARENT_ID']
                        )
                    );
                    $href = $this->lang . $parent['REALCATNAME'];

                    $this->domXml->create_element('name', trim($parent['NAME']));
                    $this->domXml->create_element('url', $href);

                    $this->getSubCatalogPath($parent['CATALOGUE_ID'], $parent['CATALOGUE_ID']);

                    $this->domXml->go_to_parent();
                }
            }

            if (!empty($item_name)) {
                $this->domXml->create_element('breadcrumbs', '', 2);
                $this->domXml->set_attribute(array('id' => 0,
                        'parent_id' => 0
                    )
                );
                $this->domXml->create_element('name', trim($item_name));
                $this->domXml->create_element('url', '');
                $this->domXml->go_to_parent();
            }
        }
    }

    public function getCompareItems($catalogue_id)
    {
        $session = new Zend_Session_Namespace('compare');

        if (!empty($session->compare[$catalogue_id])) {
            foreach ($session->compare[$catalogue_id] as $item_id => $val) {
                $item = $this->params['Item']->getItemInfo($item_id, $this->lang_id);

                $this->domXml->create_element('compare_list', '', 2);
                $this->domXml->set_attribute(array('id' => $item['ITEM_ID']
                    )
                );
                $href = $this->lang . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';

                $this->domXml->create_element('name', $item['NAME']);
                $this->domXml->create_element('brand_name', $item['BRAND_NAME']);
                $this->domXml->create_element('href', $href);

                $this->domXml->go_to_parent();
            }
        }
    }

    public function getAttrBrands($catalogue_id, $brand_id, $active_brands)
    {

        $cid = $this->work_model->getChildren($catalogue_id);
        $cid[count($cid)] = $catalogue_id;

        $models = $this->params['Item']->getAllModels($cid);

        if (!empty($models)) {
            foreach ($models as $view) {
                $is_disabled = 0;

                if (in_array($view['BRAND_ID'], $brand_id))
                    $selected = 1;
                else
                    $selected = 0;

                if (!empty($active_brands)) {
                    if (!in_array($view['BRAND_ID'], $active_brands))
                        $is_disabled = 1;
                }

                $this->domXml->create_element('attr_brands', '', 2);
                $this->domXml->set_attribute(array('id' => $view['BRAND_ID']
                , 'selected' => $selected
                , 'is_disabled' => $is_disabled
                ));

                $this->domXml->create_element('name', $view['NAME']);

                $this->domXml->go_to_parent();
            }
        }
    }

    private function getSubCatalogPath($id, $parent_id)
    {
        $result = $this->work_model->getTree($parent_id);
        if (!empty($result)) {
            foreach ($result as $view) {
                if ($id == $view['CATALOGUE_ID'])
                    continue;
                $this->domXml->create_element('breadcrumbs', '', 2);
                $this->domXml->set_attribute(array('id' => $view['CATALOGUE_ID'],
                        'parent_id' => $view['PARENT_ID']
                    )
                );
                $href = $this->lang . $view['REALCATNAME'];

                $this->domXml->create_element('name', trim($view['NAME']));
                $this->domXml->create_element('url', $href);

                $this->domXml->go_to_parent();
            }

//       $this->domXml->create_element('breadcrumbs','',2);            
//       $this->domXml->set_attribute(array('id'  => 0
//                                          ));                                           
//                                        
//       $href = $this->work_model->getCatRealCat($id);
//                                        
//       $this->domXml->create_element('name','Все производители');
//       $this->domXml->create_element('url',$href);
//        
//       $this->domXml->go_to_parent();
        }
    }

}