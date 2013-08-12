<?php
class models_MetaGenerate
{
    const SUB_LIMIT = 3;

    private $AnotherPages;
    private $Catalogue;
    private $Item;

    private $gen_catalog_id;
    private $tamplates = array();
    private $tamplates_run = array('item_meta_template', 'catalog_meta_template', 'root_catalog_meta_template');

    public function __construct()
    {
        $this->AnotherPages = new models_AnotherPages();
        $this->Catalogue = new models_Catalogue();
        $this->Item = new models_Item();

        $this->getTemplatesRun();
    }

    public function setGetCatalogId($gen_catalog_id)
    {
        $this->gen_catalog_id = $gen_catalog_id;
    }

    public function getGetCatalogId()
    {
        return $this->gen_catalog_id;
    }

    public function getTemplatesRun()
    {
        foreach ($this->tamplates_run as $view) {
            $doc_id = $this->AnotherPages->getDocByUrl($view);
            $this->tamplates[$view] = $this->AnotherPages->getDocMetaInfo($doc_id);
        }
    }

    public function genRootCatalog()
    {
        $catalog = $this->Catalogue->getTree();
        if (!empty($catalog)) {
            foreach ($catalog as $view) {
                $params['catalog'] = $view['NAME'];
                $params['sub_catalog'] = $this->getSubCats($view['CATALOGUE_ID']);

                $data['TITLE'] = $this->doReplace('root_catalog_meta_template', 'TITLE', $params);
                $data['DESC_META'] = $this->doReplace('root_catalog_meta_template', 'DESCRIPTION', $params);
                $data['KEYWORD_META'] = $this->doReplace('root_catalog_meta_template', 'KEYWORDS', $params);

                $this->Catalogue->updateCatalogue($data, $view['CATALOGUE_ID']);
            }
        }
    }

    public function genSecondCatalog()
    {
        $catalog = $this->Catalogue->getTree();
        if (!empty($catalog)) {
            foreach ($catalog as $view) {
                $childrens = $this->Catalogue->getChildren($view['CATALOGUE_ID']);
                if (!empty($childrens)) {
                    foreach ($childrens as $cld) {
                        $catalog_info = $this->Catalogue->getCatPath($cld);

                        $params['catalog'] = $catalog_info['NAME'];

                        $data['TITLE'] = $this->doReplace('catalog_meta_template', 'TITLE', $params);
                        $data['DESC_META'] = $this->doReplace('catalog_meta_template', 'DESCRIPTION', $params);
                        $data['KEYWORD_META'] = $this->doReplace('catalog_meta_template', 'KEYWORDS', $params);

                        $this->Catalogue->updateCatalogue($data, $cld);
                    }
                }
            }
        }
    }

    public function genCurrentCatalog()
    {
        $catalog_info = $this->Catalogue->getCatPath($this->gen_catalog_id);
        if (!empty($catalog_info)) {
            $params['catalog'] = $catalog_info['NAME'];

            $data['TITLE'] = $this->doReplace('catalog_meta_template', 'TITLE', $params);
            $data['DESC_META'] = $this->doReplace('catalog_meta_template', 'DESCRIPTION', $params);
            $data['KEYWORD_META'] = $this->doReplace('catalog_meta_template', 'KEYWORDS', $params);

            $this->Catalogue->updateCatalogue($data, $this->gen_catalog_id);
        }
    }

    public function genItems($is_new = false)
    {
        $gen_catalog_id = !empty($this->gen_catalog_id) ? $this->gen_catalog_id : 0;
        $items = $this->Item->getItemsForMeta($is_new, $gen_catalog_id);
        if (!empty($items)) {
            foreach ($items as $view) {
                $params['item'] = $view['NAME'];
                $params['item_type'] = $view['TYPENAME'];
                $params['catalog'] = $this->Catalogue->getCatName($view['CATALOGUE_ID']);;
                $params['brand'] = $this->Item->getBrandName($view['BRAND_ID']);;

                $data['TITLE'] = $this->doReplace('item_meta_template', 'TITLE', $params);
                $data['DESC_META'] = $this->doReplace('item_meta_template', 'DESCRIPTION', $params);
                $data['KEYWORD_META'] = $this->doReplace('item_meta_template', 'KEYWORDS', $params);

                $this->Item->updateItemImport($data, $view['ITEM_ID']);
            }
        }
    }

    private function doReplace($tamplates, $section, $params)
    {
        $text = $this->tamplates[$tamplates][$section];

        if (!empty($params['item']))
            $text = str_replace('##item##', $params['item'], $text);
        else
            $text = str_replace('##item##', '', $text);

        if (!empty($params['catalog']))
            $text = str_replace('##catalog##', $params['catalog'], $text);
        else
            $text = str_replace('##catalog##', '', $text);

        if (!empty($params['sub_catalog']))
            $text = str_replace('##sub_catalog##', $params['sub_catalog'], $text);
        else
            $text = str_replace('##sub_catalog##', '', $text);

        if (!empty($params['item_type']))
            $text = str_replace('##item_type##', $params['item_type'], $text);
        else
            $text = str_replace('##item_type##', '', $text);

        if (!empty($params['brand']))
            $text = str_replace('##brand##', $params['brand'], $text);
        else
            $text = str_replace('##brand##', '', $text);

        return $text;
    }

    private function getSubCats($id)
    {
        $catalog = $this->Catalogue->getTree($id);
        $names = array();
        if (!empty($catalog)) {
            foreach ($catalog as $key => $view) {
                if ($key == self::SUB_LIMIT) break;
                $names[] = $view['NAME'];
            }
        }

        return implode(', ', $names);
    }
}

?>