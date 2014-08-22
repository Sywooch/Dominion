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

    private function addCatSubcatalogWithBrand($cat)
    {

        $this->domXml->create_element('sub_cattree', '', 2);
        $this->domXml->set_attribute(array('catalogue_id' => $cat['CATALOGUE_ID']
        , 'parent_id' => $cat['PARENT_ID']
        ));

        $catalogHref = $cat['REALCATNAME'];

        $this->domXml->create_element('name', $cat['NAME']);
        $this->domXml->create_element('href', $catalogHref);

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


        if ($cat['BRANDS']) {
            $tmp = explode(',', $cat['BRANDS']);
            foreach ($tmp as $view) {
                list($brand, $altName) = explode('#', $view);
                $this->domXml->create_element('brand_view', '', 2);
                $this->domXml->create_element('name', $brand);

                $href = "$catalogHref$altName/";


                // Этот кусок жутко тормозит работу
//                    $_href = $AnotherPages->getSefURLbyOldURL($href);
//                    if (!empty($_href)) $href = $_href;

                $this->domXml->create_element('href', $href);

                $this->domXml->go_to_parent();
            }
        }
    }

    /**
     *
     */
    public function generateCatalogueMenu($id)
    {
        $this->domXml->create_element("catalogue-menu", "", DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED);
        $this->collectAllCatalogues($id);
        $this->domXml->go_to_parent();
    }

    /**
     * Collect all catalogues
     *
     * @param integer $catalogueId
     *
     * @return bool
     */
    private function collectAllCatalogues($catalogueId)
    {
        $catalogueList = $this->work_model->getTree($catalogueId);

        if (empty($catalogueList)) return false;

        foreach ($catalogueList as $value) {

            $this->domXml->create_element("catalogue", "", DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED);
            $this->domXml->set_attribute(array("catalog_id" => $value["CATALOGUE_ID"]));
            $this->domXml->create_element("name", $value["NAME"]);
            $this->domXml->create_element("url", $value["REALCATNAME"]);
            if (!empty($value["IMAGE_MENU"])) {
                $imageData = explode("#", $value["IMAGE_MENU"]);
                $this->domXml->create_element("image_menu", "/images/cat/" . array_shift($imageData));
                $this->domXml->set_attribute(array("width" => array_shift($imageData), "height" => array_shift($imageData)));
            }

            $this->collectAllCatalogues($value["CATALOGUE_ID"]);

            $this->domXml->go_to_parent();
        }

        return true;
    }

    /**
     * Вывести спсиок подкаталогов с брендами
     *
     * @param int $parentId Id родтеля для которго выводим список подкаталогов
     */
    public function getCatSubTree($parentId = 0)
    {


//        $topCats = $this->work_model->getTopCatalogsId($parentId);

        foreach ($this->work_model->getTopCatalogsId($parentId) as $catalogTop) {


            $cats = $this->work_model->getCatalogsIncludeBrandsList($catalogTop['CATALOGUE_ID']);
            //if (empty($cats)) return;

            if (!empty($cats)) {
                $this->addCatSubcatalogWithBrand($cats[0]);
                $this->domXml->go_to_parent();
            } else {

                $cats = $this->work_model->getCatalogsIncludeBrandsListByParent($catalogTop['CATALOGUE_ID']);

                if (!empty($cats)) {

                    $this->domXml->create_element('sub_cattree', '', 2);
                    $this->domXml->set_attribute(array('top' => 1

                    ));

                    $this->domXml->create_element('name', $catalogTop['NAME']);

                    foreach ($cats as $cat) {
                        $this->addCatSubcatalogWithBrand($cat);
                        $this->domXml->go_to_parent();
                    }
                    $this->domXml->go_to_parent();
                }


//                $this->domXml->create_element('sub_cattree', '', 2);
//                $this->domXml->set_attribute(array(
//                    'catalogue_id' => $catalogTop['CATALOGUE_ID']
////              , 'parent_id' => $cat['PARENT_ID']
//                ));
//                $this->domXml->create_element('name', $catalogTop['NAME']);
            }

//            $this->domXml->go_to_parent();
            continue;
//            $AnotherPages = new models_AnotherPages();

//            foreach ($cats as $cat) {
//
//                $this->domXml->create_element('sub_cattree', '', 2);
//                $this->domXml->set_attribute(array('catalogue_id' => $cat['CATALOGUE_ID']
//                , 'parent_id' => $cat['PARENT_ID']
//                ));
//
//                $catalogHref = $cat['REALCATNAME'];
//
//                $this->domXml->create_element('name', $cat['NAME']);
//                $this->domXml->create_element('href', $catalogHref);
//
//                if (!empty($cat['IMAGE1']) && strchr($cat['IMAGE1'], "#")) {
//                    $tmp = explode('#', $cat['IMAGE1']);
//                    $this->domXml->create_element('image', '', 2);
//                    $this->domXml->set_attribute(array('src' => $tmp[0],
//                            'w' => $tmp[1],
//                            'h' => $tmp[2]
//                        )
//                    );
//                    $this->domXml->go_to_parent();
//                }
//
//
//                if ($cat['BRANDS']) {
//                    $tmp = explode(',', $cat['BRANDS']);
//                    foreach ($tmp as $view) {
//                        list($brand, $altName) = explode('#', $view);
//                        $this->domXml->create_element('brand_view', '', 2);
//                        $this->domXml->create_element('name', $brand);
//
//                        $href = "$catalogHref$altName/";
//
//
//                        // Этот кусок жутко тормозит работу
////                    $_href = $AnotherPages->getSefURLbyOldURL($href);
////                    if (!empty($_href)) $href = $_href;
//
//                        $this->domXml->create_element('href', $href);
//
//                        $this->domXml->go_to_parent();
//                    }
//                }
//
//                $this->domXml->go_to_parent();
//            }


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

    /**
     * Get catalog path
     *
     * @param        $id
     * @param string $item_name
     */
    public function getCatalogPath($id, $item_name = '')
    {
        $parent = $id;

        $this->domXml->create_element('breadcrumbs', '', 2);
        $this->domXml->set_attribute(array('id' => 0,
                'parent_id' => 0
            )
        );

        while ((($cat = $this->work_model->getParents($parent, $this->lang_id)) != null) && ($cat["CATALOGUE_ID"] != 0)) {
            $this->domXml->create_element('crumbs', '', 2);
            $this->domXml->set_attribute(
                array('id' => $cat['CATALOGUE_ID'],
                    'parent_id' => $cat['PARENT_ID']
                )
            );

            $this->domXml->create_element('name', trim($cat['NAME']));

            if ($id != $parent) {
                $this->domXml->create_element('url', $this->lang . $cat['REALCATNAME']);
            }

            $this->domXml->go_to_parent();

            $parent = $cat["PARENT_ID"];
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

        $this->domXml->go_to_parent();
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

    public function getCatalogueProducts()
    {

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