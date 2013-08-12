<?php
class ItemController extends App_Controller_Frontend_Action
{

    private $item_id;
    private $tabs = array('description' => 'Описание'
    , 'characteristics' => 'Характеристики'
    , 'video' => 'Видеообзор'
    , 'items' => 'С этим товаром покупают'
    , 'comments' => 'Отзывы'
    );

    public $Attributs;


    function init()
    {
        parent::init();

        $Item = new models_Item();
        $Catalogue = new models_Catalogue();

        $this->item_id = $this->_getParam('id');
        $catalogue_id = $Item->getItemCatalog($this->item_id);
        $parent_active = $Catalogue->getatalogActive($catalogue_id);
        if ($this->item_id == 0 || !$parent_active) {
            $this->page_404();
        }

        $res = '';
        if (!empty($this->item_id)) $res = $Item->getItemInfo($this->item_id);

        if ($this->item_id === false || ($this->item_id > 0 && empty($res))) {
            $this->page_404();
        }
    }

    public function viewAction()
    {
        $Item = new models_Item();
        $Catalogue = new models_Catalogue();
        $SectionAlign = new models_SectionAlign();

        $catalogue_id = 0;
        $pathIDs = array();

        $catalogue_id = $Item->getItemCatalog($this->item_id);
        $comments_count = $Item->getCountItemResponses($this->item_id);

        $params['currency'] = $this->currency;
        $params['work_controller'] = 'item';
        $params['input_id'] = $this->item_id;

        $o_data['id'] = $this->item_id;
        $o_data['catalogue_id'] = $catalogue_id;
        $o_data['currency'] = $this->currency;
        $o_data['comments_count'] = $comments_count;
        $o_data['category_path'] = $Catalogue->getCatRealCat($catalogue_id);

        $this->openData($o_data);

        $params['Item'] = $Item;

        $cat_helper = $this->_helper->helperLoader('Catalogue', $params);
        $cat_helper->setModel($Catalogue);
        $cat_helper->setDomXml($this->domXml);
        $cat_helper->getCompareItems($catalogue_id);
        $this->domXml = $cat_helper->getDomXml();

        $bn_helper = $this->_helper->helperLoader('Banners');
        $bn_helper->setModel($SectionAlign);
        $bn_helper->setDomXml($this->domXml);
        $bn_helper->getBanners('banner_social_likes', 22, 20);
//      $bn_helper->getBanners('banner_item_pay',9,11);
//      $bn_helper->getBanners('banner_item_live_help',9,12);
        $bn_helper->getCatalogueBanner($catalogue_id);
        $this->domXml = $bn_helper->getDomXml();

        $tab_section = $this->_getParam('section');

        $it_helper = $this->_helper->helperLoader('Item', $params);
        $it_helper->setModel($Item);
        $it_helper->setDomXml($this->domXml);
        $it_helper->getDocPath($this->item_id);
        $it_helper->getItemMeta($this->item_id);
        $it_helper->getItemInfo($this->item_id);
        $it_helper->getTabs($tab_section);
        $this->domXml = $it_helper->getDomXml();

        $attr = array();

        $sh_params['view'] = $this->view;
        $sh_params['per_page'] = $this->getSettingValue('similar_item_block_count');
        $sh_params['currency'] = $this->currency;

        $shat_helper = $this->_helper->helperLoader('ShortAttributs', $sh_params);
        $shat_helper->setModel($Item);
        $shat_helper->setDomXml($this->domXml);
        $shat_helper->getShortAttributs($this->item_id, $catalogue_id);
        $html = $shat_helper->getSimilarItem($this->item_id, $attr);
        $this->domXml = $shat_helper->getDomXml();
    }

    public function reserveAction()
    {
        $Item = new models_Item();
        $AnotherPages = new models_AnotherPages();

        $item_id = $this->_getParam('id');
        $item_name = '';
        $system_message = '';
        $error = 0;

        $request = $this->getRequest();

        $item_info = $Item->getItemInfo($item_id);
        if (!empty($item_info)) {
            $item_name = $item_info['BRAND_NAME'] . ' ' . $item_info['NAME'];
            if (!empty($item_info['TYPENAME'])) $item_name = $item_info['TYPENAME'] . ' ' . $item_name;
        }

        $doc_id = $AnotherPages->getDocByUrl('/reserve/');
        $system_message = $AnotherPages->getDocXml($doc_id, 0);
        $replace = array('##name##' => $item_name);

        $system_message = strtr($system_message, $replace);

        if ($request->isPost()) {
            $reserve_email = $request->getPost('reserve_email');
            if (empty($reserve_email)) {
                $system_message .= '<p><b style="color: red;">Укажите E-mail</b></p>';
                $error = 1;
            }

            if (!empty($reserve_email)) {
                if (!preg_match("/[a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+){1,}/", $reserve_email)) {
                    $system_message .= '<p><b style="color: red;">Не верный формат E-mail</b></p>';
                    $error = 1;
                }
            }

            if ($error == 0) {
                $insert_data['ITEM_ID'] = $item_id;
                $insert_data['EMAIL'] = $reserve_email;
                $insert_data['STATUS'] = 0;

                if (!$Item->hasItemReserved($insert_data)) {
                    $Item->insertItemN('ITEM_REQUEST', $insert_data);
                }

                $system_message .= '<p><b style="color: blue;">Спасибо за заявку</b></p>';
                echo $system_message;
            }
        } else {
            $this->domXml->create_element('page', "", 1);
            $this->domXml->set_tag('//page', true);

            $this->domXml->create_element('error', $error);
            $this->domXml->create_element('item_id', $item_id);
            $this->domXml->create_element('system_message', $system_message);
        }
    }

    public function getsimilaritemAction()
    {
        $Item = new models_Item();
        $this->_helper->viewRenderer->setNoRender();

        $request = $this->getRequest();
        $result['vals'] = '';
        $result['html'] = '';

        if ($request->isGet()) {
            $attr = array();

            $at = $request->getQuery('attr');

            $attarray = explode('a', $at);
            if (!empty($attarray)) {
                foreach ($attarray as $param) {
                    if (preg_match('/(\w+)v(\w+)/', $param, $m)) {
                        $attr[$m[1]] = $m[2];
                    }
                }
            }

            $params['view'] = $this->view;
            $params['per_page'] = $this->getSettingValue('similar_item_block_count');
            $params['currency'] = $this->currency;

            $shat_helper = $this->_helper->helperLoader('ShortAttributs', $params);
            $shat_helper->setModel($Item);
            $shat_helper->setDomXml($this->domXml);
            $result['vals'] = $shat_helper->shortAttribMode($this->item_id, $attr);
            $result['html'] = $shat_helper->getSimilarItem($this->item_id, $attr);
            $this->domXml = $shat_helper->getDomXml();
        }

        $this->_helper->json($result);
    }
}