<?php

class AjaxController extends Zend_Controller_Action
{

    public $AnotherPages;
    public $Textes;
    public $SystemSets;
    public $Registration;
    public $Faq;
    public $Item;
    public $Catalogue;

    public $lang_id;
    public $currency;

    public $template;

    function init()
    {
        $this->work_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $this->work_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        $this->_helper->viewRenderer->setNoRender();

        $this->AnotherPages = new models_AnotherPages();
        $this->SystemSets = new models_SystemSets();
        $this->Textes = new models_Textes();
        $this->Registration = new models_Registration();
        $this->Item = new models_Item();
        $this->Catalogue = new models_Catalogue();

        $this->lang_id = '';
        $this->currency = 1;
    }

    public function caphainpAction()
    {
        $request = $this->getRequest();
        $captcha = $request->getQuery('captcha');
        if (!empty($captcha)) {
            if ($captcha == $_SESSION['biz_captcha']) echo 'true';
            else echo 'false';

        } else echo 'false';
    }

    public function forgotAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $member_data = $this->Registration->check_email($postData['forgot_email']);
            if (!empty($member_data)) {
                $doc_id = $this->AnotherPages->getDocId('/forgot/');
                $message = $this->AnotherPages->getDocXml($doc_id, 0);

                if (!empty($member_data['email'])) $message = str_replace('##email##', $member_data['EMAIL'], $message);
                else $message = str_replace('##email##', '', $message);

                if (!empty($member_data['passwd'])) $message = str_replace('##passwd##', $member_data['PASSWORD'], $message);
                else $message = str_replace('##passwd##', '', $message);

                $message = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
                    . $message . '</body></html>';

                $email_from = $this->getSettingValue('email_from');
                $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
                preg_match_all($patrern, $email_from, $arr);

                $params['to'] = $postData['forgot_email'];
                $params['message'] = $message;
                $params['subject'] = 'Система напоминания пароля';
                $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
                $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

                App_Mail::send($params);

                echo 1;
            } else echo 0;
        }
    }

    public function getcallAction()
    {
        $request = $this->getRequest();

        $result = '';
        if ($request->isPost()) {
            $insert_data['NAME'] = $request->getPost('call_name');
            $insert_data['PHONE'] = $request->getPost('call_phone');
            $insert_data['DESCRIPTION'] = $request->getPost('call_time');

            $this->AnotherPages->insertSomeData('CALLBACK', $insert_data);

            if (!empty($insert_data)) {
                $doc_id = $this->AnotherPages->getDocByUrl('/getcall/');
                $message = $this->AnotherPages->getDocXml($doc_id, 0);

                if (!empty($insert_data['NAME'])) $message = str_replace('##call_name##', $insert_data['NAME'], $message);
                else $message = str_replace('##call_name##', '', $message);

                if (!empty($insert_data['PHONE'])) $message = str_replace('##call_phone##', $insert_data['PHONE'], $message);
                else $message = str_replace('##call_phone##', '', $message);

                if (!empty($insert_data['DESCRIPTION'])) $message = str_replace('##call_time##', $insert_data['DESCRIPTION'], $message);
                else $message = str_replace('##call_time##', '', $message);

                $message = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
                    . $message . '</body></html>';


                $getcall_email_managers = $this->getSettingValue('getcall_email_managers');
                if ($getcall_email_managers) {
                    $manager_emails_arr = explode(";", $getcall_email_managers);
                    if (!empty($manager_emails_arr)) {
                        $email_from = $this->getSettingValue('email_from');
                        $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
                        preg_match_all($patrern, $email_from, $arr);

                        foreach ($manager_emails_arr as $mm) {
                            $mm = trim($mm);
                            if (!empty($mm)) {
                                $params['to'] = $mm;
                                $params['message'] = $message;
                                $params['subject'] = 'Заявка на обратный звонок';
                                $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
                                $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

                                App_Mail::send($params);
                            }
                        }
                    }
                }
            }

            $result = 'Ваш запрос принят';
        }

        echo $result;
    }

    public function addcartAction()
    {
        $request = $this->getRequest();
        $count = 0;
        $sum = 0;

        $result = array();

        $curr_info = $this->Item->getCurrencyInfo($this->currency);

        if ($request->isGet()) {
            $item_id = $request->getQuery('id');
            $count = $request->getQuery('count');

            $params['currency'] = $this->currency;

            $ct_helper = $this->_helper->helperLoader('Cart', $params);
            $ct_helper->setModel($this->Item);
            $ct_helper->addCart($item_id, $count);

            list($count, $sum) = $ct_helper->getBasket();
            $sum = round($sum);
        }

        $item_name = $this->Item->getCartItemName($item_id);
        $result['add_cart_message'] = 'В корзину добавлен ' . $item_name . '<br /><a href="/cart/">Оформить заказ</a>';
        $result['html'] = '<a href="/cart/">Корзина<br/><span>Товаров ' . $count . ' &mdash; ' . $sum . ' ' . $curr_info['SNAME'] . '</span></a>';
        $this->_helper->json($result);
    }

    public function recountcartAction()
    {
        $request = $this->getRequest();
        $result = array();
        $total_summ = 0;

        if ($request->isGet()) {
            $curr_info = $this->Item->getCurrencyInfo($this->currency);

            $item_id = $request->getQuery('id');
            $itm_count = $request->getQuery('itm_count');

            $session = new Zend_Session_Namespace('cart');

            $params['currency'] = $this->currency;

            $ct_helper = $this->_helper->helperLoader('Cart', $params);
            $ct_helper->setModel($this->Item);

            if ($itm_count == 'delete') {
                if (isset($session->item[$item_id])) {
                    unset($session->item[$item_id]);
                }

            } else {
                if (isset($session->item[$item_id])) {
                    $item = $this->Item->getItemInfo($item_id);

                    $item['sh_disc_img_small'] = '';
                    $item['sh_disc_img_big'] = '';
                    $item['has_discount'] = 0;

                    $curr_info = $this->Item->getCurrencyInfo($this->currency);
                    list($new_price, $new_price1) = $this->Item->recountPrice($item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->currency, $curr_info['PRICE']);

                    if ($this->currency > 1) {
                        $item['iprice'] = round($new_price, 1);
                        $item['iprice1'] = round($new_price1, 1);
                    } else {
                        $item['iprice'] = round($new_price);
                        $item['iprice1'] = round($new_price1);
                    }

                    $item = $ct_helper->recountPrice($item);

                    $resul_price = ($item['iprice1'] > 0) ? $item['iprice1'] : $item['iprice'];

                    $session->item[$item_id]['count'] = $itm_count;
                    $total_summ = round($itm_count * $resul_price);
                }
            }

            list($count, $sum) = $ct_helper->getBasket();

            $sum = round($sum);

            $result['total_summ'] = $total_summ;
            $result['itogo_summa'] = $sum;

            $result['count'] = $count;
            if ($count > 0) {
                $result['html'] = '<a href="/cart/">Корзина<br/><span>Товаров ' . $count . ' &mdash; ' . $sum . ' ' . $curr_info['SNAME'] . '</span></a>';
            } else {
                $result['html'] = '<a href="/cart/">Корзина<br/><span>Товаров 0';
            }

        }

        $this->_helper->json($result);
    }

    public function updatedataAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $insert_data['firma'] = $postData['firma'];
            $insert_data['name'] = $postData['name'];
            $insert_data['address'] = $postData['address'];
            $insert_data['phone'] = $postData['phone'];
            $insert_data['email'] = $postData['email'];
            $insert_data['urist_fiz'] = $postData['urist_fiz'];

            if (!empty($postData['passwd'])) $insert_data['passwd'] = $postData['passwd'];

            $_user_data = Zend_Auth::getInstance()->getIdentity();

            $user_data = array('user_name' => $insert_data['name'],
                'user_address' => $insert_data['address'],
                'user_id' => $_user_data['user_id'],
                'user_phone' => $insert_data['phone'],
                'user_email' => $insert_data['email'],
                'user_urist_fiz' => $insert_data['urist_fiz'],
                array('Login succesful'));

            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user_data);

            $this->Registration->updateRegData($insert_data, $_user_data['user_id']);

            echo 'Данные обновлены';
        }
    }

    public function compareAction()
    {
        $html = '';

        $request = $this->getRequest();

        if ($request->isPost()) {
            $item_id = $request->getPost('id');
            $checked = $request->getPost('checked');
            $session = new Zend_Session_Namespace('compare');

            $catalogue_id = $this->Item->getItemCatalog($item_id);

            if ($checked == 1) {
                if (!isset($session->compare[$catalogue_id][$item_id])) {
                    $session->compare[$catalogue_id][$item_id] = 1;
                }
            } else {
                if (isset($session->compare[$catalogue_id][$item_id]))
                    unset($session->compare[$catalogue_id][$item_id]);
            }
        }

        if (!empty($session->compare[$catalogue_id])) {
            $html = '<h3>Товары в сравнении</h3><ul class="menu">';
            foreach ($session->compare[$catalogue_id] as $item_id => $val) {
                $item = $this->Item->getItemInfo($item_id, $this->lang_id);

                $href = $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';

                $html .= '<li><a href="#" xid="' . $item['ITEM_ID'] . '" class="close_icon delete_compare">Close</a>';
                $html .= '<h3><a href="' . $href . '">' . $item['BRAND_NAME'] . '&#160;' . $item['NAME'] . '</a></h3></li>';
            }

            if (count($session->compare[$catalogue_id]) > 1) $html .= '</ul><a href="/compare/' . $catalogue_id . '/" class="button_link">Сравнить</a>';
        }

        echo $html;
    }

    public function siginAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            if (!empty($postData)) {
                $username = $postData['login_email'];
                $password = $postData['login_password'];

                $adapter = new App_Auth_Authentication($username, $password);
                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate($adapter);
                if (!$result->isValid()) {
                    echo 0;
                } else {
                    $result->getIdentity() === $auth->getIdentity();
                    $user_data = $result->getIdentity();
                    $auth->getStorage()->write($user_data);

                    echo 1;
                }
            } else {
                echo 0;
            }

        }
    }

    public function popupAction()
    {
        $Article = new models_Article();
        $AnotherPages = new models_AnotherPages();
        $mode = $this->_getParam('mode');
        $id = $this->_getParam('id');

        $text = '';
        switch ($mode) {
            case 'warranty':
                $text = $Article->getPopUpText(array('WARRANTY' => 'WARRANTY_ID'), $id);
                break;
            case 'delivery':
                $text = $Article->getPopUpText(array('DELIVERY' => 'DELIVERY_ID'), $id);
                break;
            case 'credit':
                $text = $Article->getPopUpText(array('CREDIT' => 'CREDIT_ID'), $id);
                break;
            case 'userdiscount':
                $text = $AnotherPages->getDocXml($id, 9);
                break;
        }

        echo $text;
    }

    public function commentsAction()
    {
        $itm = $this->_getParam('item_id');

        $json_back['res'] = 0;
        $request = $this->getRequest();

        if ($request->isGet()) {
            $error = array();
            $insert['NAME'] = $request->getQuery('comment_name');
            $insert['DESCRIPTION'] = $request->getQuery('comment_comment');

            if (empty($insert['NAME'])) {
                $error[count($error)] = 'Поле имя пустое';
            }

            if (empty($insert['DESCRIPTION'])) {
                $error[count($error)] = 'Поле мнение пустое';
            }

            if (empty($error)) {
                $insert['ITEM_ID'] = $itm;
                $insert['DATA'] = date("Y-m-d H:i:s");
                $insert['HIDE'] = 1;

                $this->Item->insertResponseData($insert);

                $response_count = $this->Item->getCountItemResponses($itm);

                $json_back['res'] = 1;
                $json_back['count'] = $response_count;

                $json_back['html_code'] = '<div class="comment_block">'
                    . '<div class="comment_header">'
                    . '<h3>' . $insert['NAME'] . '</h3>'
                    . '<span class="date">' . date('d/m/Y H:i') . '</span>'
                    . '</div><p>' . $insert['DESCRIPTION'] . '</p></div>';

                $this->sendMenegerComment($itm, $insert);
            }
        }
        echo json_encode($json_back);
        exit;
    }

    /**
     * Get count attributes
     *
     * @return array
     */
    public function getattrcountAction()
    {
        $params = $this->getRequest()->getQuery();

        if (empty($params) || empty($params['catalogue_id'])) return;

        /** @var $objectValueSelection Helpers_ObjectValue_ObjectValueSelection */
        $objectValueSelection = $this->_helper->helperLoader("ObjectValue_ObjectValueSelection");

        if (!empty($params['pmin']) && !empty($params['pmax'])) {
            list($minPrice, $maxPrice) = Format_ConvertDataElasticSelection::getFormatRecountPrice
                (
                    $params['pmin'],
                    $params['pmax'],
                    $this->currency,
                    $this->_helper->helperLoader('ItemSelectionPrice')
                );
            $objectValueSelection->setDataSlider("ATTRIBUTES.price", $minPrice, $maxPrice);
        }

        if (!empty($params['br']) || !empty($params['at'])) {
            $resultAttributes = Format_ConvertDataElasticSelection::getArrayAttributes(
                $params['at'] . $params['br']
            );

            $objectValueSelection->setDataBrands($resultAttributes['brands']);
            $objectValueSelection->setDataAttributesDouble($resultAttributes[Format_ConvertDataElasticSelection::NAME_ATTRIBUTES_DOUBLE]);
            $objectValueSelection->setDataAttributesUnique($resultAttributes[Format_ConvertDataElasticSelection::NAME_ATRIBUTES_UNIQUE]);
        }

        $objectValueSelection->setCatalogueID($params['catalogue_id']);

        $parameters = Zend_Registry::get("config")->toArray();

        /** @var $helpersSelectionElasticSearch Helpers_SelectionElasticSearch */
        $helpersSelectionElasticSearch = $this->_helper->helperLoader("SelectionElasticSearch");
        $helpersSelectionElasticSearch->connect($parameters['search_engine'], "selection");
        $helpersSelectionElasticSearch->selection($objectValueSelection);

        return $this->_helper->json(Format_ConvertDataElasticSelection::getFormatResultData(
                $helpersSelectionElasticSearch->getAttributes(),
                $helpersSelectionElasticSearch->getBrands()
            )
        );
    }

//    public function attritemcountAction()
//    {
//        $request = $this->getRequest();
//        $attr = array();
//        $brands = array();
//        $result['brands_count'] = 0;
//        $result['attrib_count'] = 0;
//        $result['items_count'] = 0;
//
//        $result['current_min_price'] = 0;
//        $result['current_max_price'] = 0;
//
//        if ($request->isGet()) {
//            $postData = $request->getQuery();
//
//            if (!empty($postData['br'])) {
//                preg_match_all('/b(\d+)/', $postData['br'], $out);
//                if (!empty($out[1])) {
//                    $brands = $out[1];
//                }
//            }
//
//            if (!empty($postData['at'])) {
//                $Attributs = new models_Attributs();
//                $Item = new models_Item();
//                $Catalogue = new models_Catalogue();
//
//                $_catalogue_id = $Catalogue->getChildren($postData['catalogue_id']);
//                $_catalogue_id[count($_catalogue_id)] = $postData['catalogue_id'];
//                $_items = $Item->getCatalogItemsID($_catalogue_id);
//
//                $params['at'] = $postData['at'];
//                $params['items'] = $_items;
//
//                $attr = $Attributs->getAllAttrForSelection($params);
//
//                unset($params);
//            }
//
//            $params['catalogue_id'] = $postData['catalogue_id'];
//            $params['brands'] = $brands;
//
//            $isp_params['currency_id'] = 2;
//            $isp_params['real_currency_id'] = $this->currency;
//            $isp_price['min_price'] = $postData['pmin'];
//            $isp_price['max_price'] = $postData['pmax'];
//
//            // Перерасчет цен в валюту товаров
//            $isp_helper = $this->_helper->helperLoader('ItemSelectionPrice');
//            list($params['pmin'], $params['pmax']) = $isp_helper->recountPrice($isp_price, $isp_params);
//
//
//            $params['nat_pmin'] = $postData['pmin'];
//            $params['nat_pmax'] = $postData['pmax'];
//            $params['currency_id'] = $this->currency;
//
//            $is_helper = $this->_helper->helperLoader('ItemSelection', $params);
//            $is_helper->getItemSelection($attr);
//            list($result['brands'], $result['attrib']) = $is_helper->getAttributsForActive();
////        list($active_brands, $active_attrib) = $is_helper->getAttributsForActive();
//            $active_brands = $result['brands'];
//            $active_attrib = $result['attrib'];
//
//            $active_items = $is_helper->getItemsResultId();
//
//            $result['items_count'] = $is_helper->getItemsResultCount();
//            $result['brands_count'] = count($result['brands']);
//            $result['attrib_count'] = count($result['attrib']);
//
//            $isp_params['currency_id'] = $this->currency;
//            $isp_params['real_currency_id'] = 2;
//            $isp_params['catalogue_id'] = $postData['catalogue_id'];
//            $isp_params['brands'] = $active_brands;
//            $isp_params['items_id'] = $active_items;
//
//            $isp_helper = $this->_helper->helperLoader('ItemSelectionPrice');
//            $current_min_max_price = $isp_helper->getPrices($isp_params);
//
//            $result['current_min_price'] = $current_min_max_price['min_price'];
//            $result['current_max_price'] = $current_min_max_price['max_price'];
//        }
//
//        $this->_helper->json($result);
//    }

    public function goAction()
    {
        $request = $this->getRequest();
        $url = '/';
        if ($request->isGet()) {
            $url = $request->getQuery('url');
        }

        $this->_redirect($url);
    }

    public function mapAction()
    {
        $map_code = '';

        $SectionAlign = new models_SectionAlign();
        $result = $SectionAlign->getBanners(21, 19);
        if (!empty($result)) {
            foreach ($result as $view) {
                switch ($view['TYPE']) {
                    case 0:
                        $image = explode('#', $view['IMAGE1']);
                        $map_code .= '<img src="/images/bn/' . $image[0] . '" width="' . $image[1] . '" heigth="' . $image[2] . '"/>';
                        break;
                    case 2:
                        $map_code .= $view['DESCRIPTION'];
                        break;
                }
            }
        }

        echo $map_code;
    }

    public function getrangeattrAction()
    {
        $_url = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $Item = new models_Item();
            $Catalogue = new models_Catalogue();
            $Attributs = new models_Attributs();

            $min = $request->getPost('min');
            $max = $request->getPost('max');
            $catalogue_id = $request->getPost('catalogue_id');
            $xid = $request->getPost('xid');

            $_catalogue_id = $Catalogue->getChildren($catalogue_id);
            $_catalogue_id[count($_catalogue_id)] = $catalogue_id;
            $_items = $Item->getCatalogItemsID($_catalogue_id);

            $params['min'] = $min;
            $params['max'] = $max;
            $params['attribut_id'] = $xid;
            $params['items'] = $_items;

            $_min = $Attributs->getMinAttrRange($params);
            $_max = $Attributs->getMaxAttrRange($params);

            if (!empty($_min) && !empty($_max))
                $_url = 'a' . $xid . 'v' . $_min . '-' . $_max;
        }

        echo $_url;
    }

    private function sendMenegerComment($itm, $data)
    {
        $item = $this->Item->getItemInfo($itm);

        $item_page = $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
        $item_admin = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/RESPONSES.php?pid=' . $itm . '&p=1';

        $doc_id = $this->AnotherPages->getDocByUrl('/comments_email/');
        $message_admin = $this->AnotherPages->getDocXml($doc_id, 0);

        $replace = array('##name##' => $data['NAME']
        , '##message##' => $data['DESCRIPTION']
        , '##item_page##' => $item_page
        , '##item_admin##' => $item_admin
        );

        $message_admin = strtr($message_admin, $replace);

        $message_admin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
            . $message_admin . '</body></html>';

        $comments_email_managers = $this->getSettingValue('comments_email_managers');
        if ($comments_email_managers) {
            $manager_emails_arr = explode(";", $comments_email_managers);
            if (!empty($manager_emails_arr)) {
                $email_from = $this->getSettingValue('email_from');
                $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
                preg_match_all($patrern, $email_from, $arr);

                foreach ($manager_emails_arr as $mm) {
                    $mm = trim($mm);
                    if (!empty($mm)) {
                        $params['to'] = $mm;
                        $params['message'] = $message_admin;
                        $params['subject'] = 'Добавление нового коментария';
                        $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
                        $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

                        App_Mail::send($params);
                    }
                }
            }
        }
    }

    private function processFormData($data)
    {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $view) {
                if (!is_array($view)) {
                    $data[$key] = strip_tags($view);
                    $data[$key] = addslashes($view);
                }
            }
        }

        return $data;
    }

    public function getSettingValue($name)
    {
        return $this->SystemSets->getSettingValue($name);
    }

    public function makemetaAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $incomingData = $request->getPost();

            $MetaGenerate = new models_MetaGenerate();

            switch ($incomingData['gen_type']) {
                //всего
                case 0:
                    $MetaGenerate->genRootCatalog();
                    $MetaGenerate->genSecondCatalog();
                    $MetaGenerate->genItems();
                    break;

                //всего каталога
                case 1:
                    $MetaGenerate->genRootCatalog();
                    $MetaGenerate->genSecondCatalog();
                    break;

                //каталога 1го уровня
                case 2:
                    $MetaGenerate->genRootCatalog();
                    break;

                //каталога 2го уровня и выше
                case 3:
                    $MetaGenerate->genSecondCatalog();
                    break;

                //указанного каталога
                case 4:
                    $MetaGenerate->setGetCatalogId($incomingData['gen_catalog_id']);
                    $MetaGenerate->genItems();
                    break;

                //всех товаров
                case 5:
                    $MetaGenerate->genItems();
                    break;

                //новых товаров
                case 6:
                    $MetaGenerate->genItems(true);
                    break;
            }

        }
    }

}