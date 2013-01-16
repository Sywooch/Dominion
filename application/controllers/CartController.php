<?php

class CartController extends App_Controller_Frontend_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $AnotherPages = new models_AnotherPages();
        $Item = new models_Item();

        $doc_id = $AnotherPages->getDocByUrl('/cart/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);

        $this->getDocPath($doc_id);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $params['currency'] = $this->currency;

        $cr_helper = $this->_helper->helperLoader('Cart', $params);
        $cr_helper->setModel($Item);
        $cr_helper->setDomXml($this->domXml);
        $cr_helper->getPayments();
        $cr_helper->getCart();
        $this->domXml = $cr_helper->getDomXml();
    }

    public function orderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $Item = new models_Item();
            $Cart = new models_Cart();
            $AnotherPages = new models_AnotherPages();

            $wmz_payment = $this->getSettingValue('wmz_payment') ? $this->getSettingValue('wmz_payment') : 15;

            $zakaz_data['PAYMENT'] = $request->getPost('payment');
            $zakaz_data['NAME'] = $request->getPost('name');
            $zakaz_data['EMAIL'] = $request->getPost('email');
            $zakaz_data['TELMOB'] = $request->getPost('telmob');
            $zakaz_data['ADDRESS'] = $request->getPost('address');
            $zakaz_data['INFO'] = $request->getPost('info');

            $user_data = Zend_Auth::getInstance()->getIdentity();
            if (!empty($user_data)) {
                $zakaz_data['USER_ID'] = $user_data['user_id'];
            }

            $zakaz_data['DATA'] = date("Y-m-d H:i:s");
            $zakaz_data['DELIVERYDATA'] = date("Y-m-d H:i:s");


            $zakaz_id = $Item->insertZakaz($zakaz_data);

            $_SESSION['ses_zakaz_id'] = $zakaz_id;

            $session = new Zend_Session_Namespace('cart');

            if (!empty($session->item)) {
                $curr_info = $Item->getCurrencyInfo($this->currency);

                $table = "<p>Номер Вашего заказа <b>№{$zakaz_id}</b></p>
                  <table cellspacing='0' cellpadding='2' border='1'>
                  <tbody>
                    <tr>
                        <th>Наименование товара</th>
                        <th>Цена, {$curr_info['SNAME']}</th>
                        <th>Количество</th>
                        <th>Стоимость, {$curr_info['SNAME']}</th>
                    </tr>";

                $total_price = 0;
                $total_price_nds = 0;

                $vCeneIDItems = array();

                foreach ($session->item as $item_id => $view) {
                    $item = $Item->getItemInfo($item_id);

                    list($new_price, $new_price1) = $Item->recountPrice($item['PRICE'],
                                                                        $item['PRICE1'],
                                                                        $item['CURRENCY_ID'],
                                                                        $this->currency,
                                                                        $curr_info['PRICE']);

                    $item['sh_disc_img_small'] = '';
                    $item['sh_disc_img_big'] = '';
                    $item['has_discount'] = 0;

                    if ($this->currency > 1) {
                        $item['iprice'] = round($new_price, 1);
                        $item['iprice1'] = round($new_price1, 1);
                    } else {
                        $item['iprice'] = round($new_price);
                        $item['iprice1'] = round($new_price1);
                    }

                    $params['currency'] = $this->currency;
                    $ct_helper = $this->_helper->helperLoader('Cart', $params);
                    $ct_helper->setModel($Item);
                    $item = $ct_helper->recountPrice($item);

                    $resul_price = ($item['iprice1'] > 0) ? $item['iprice1'] : $item['iprice'];

                    $_item_name = $item['TYPENAME'] . ' ' . $item['BRAND_NAME'] . ' ' . $item['NAME'];

                    $zakaz_item['ZAKAZ_ID'] = $zakaz_id;
                    $zakaz_item['CATALOGUE_ID'] = $item['CATALOGUE_ID'];
                    $zakaz_item['NAME'] = $_item_name;
                    $zakaz_item['ITEM_ID'] = $item_id;

                    // Собираем ID товара
                    $vCeneIDItems[] = $item['ARTICLE'];

                    $item_curr_info = $Item->getCurrencyInfo($item['CURRENCY_ID']);

                    $zakaz_item['CURRENCY_ID'] = $this->currency;
                    $zakaz_item['PRICE'] = $resul_price;
                    $zakaz_item['PURCHASE_PRICE'] = 0;
                    $zakaz_item['ITEM_PRICE'] = ($item['PRICE1'] > 0) ? $item['PRICE1'] : $item['PRICE'];
                    $zakaz_item['ITEM_CURRENCY'] = $item_curr_info['SNAME'];
                    $zakaz_item['QUANTITY'] = $view['count'];
                    $zakaz_item['COST'] = $resul_price * $view['count'];

                    $href = 'http://7560000.com.ua/' . $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';

                    $table.="<tr>
                    <td><a href='{$href}'>" . $_item_name . "</a></td>
                    <td>" . $resul_price . " {$curr_info['SNAME']} </td>
                    <td>" . $view['count'] . "</td>
                    <td>" . $resul_price * $view['count'] . " {$curr_info['SNAME']}</td>
                  </tr>";

                    $Item->insertOrder($zakaz_item);

                    $total_price+=$resul_price * $view['count'];
                }

                $table.="<tr>
                    <th colspan='3'>&nbsp;</th>
                    <th align=\"left\">
                        Итого:" . $total_price . " {$curr_info['SNAME']}
                  </tr>";

                $table.="</tbody></table><br>";

                $table.="<p>Итоговая сумма заказа: " . $total_price . " " . $curr_info['SNAME'] . "</p>";

                if (!empty($zakaz_data['EMAIL'])) {
                    $doc_id = $AnotherPages->getDocByUrl('order_buy');
                    $message = $AnotherPages->getDocXml($doc_id, 0);

                    $message = str_replace('##table##', $table, $message);

                    $message = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
                            . $message . '</body></html>';

                    $params['to'] = $zakaz_data['EMAIL'];
                    $params['message'] = $message;

                    $email_from = $this->getSettingValue('email_from');
                    $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
                    preg_match_all($patrern, $email_from, $arr);

                    $params['subject'] = 'Оформление заказа';
                    $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
                    $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

                    App_Mail::send($params);
                }

                $_payment = $Cart->getPaymentInfo($zakaz_data['PAYMENT']);

                if (!empty($_payment['NAME'])) {
                    $zakaz_data['_payment'] = $_payment['NAME'];
                }


                $def_order_email = $this->getSettingValue('manager_emails');
                $manager_emails = explode(";", $def_order_email);
                $message_admin = $this->getManagerLetter($table, $zakaz_data);
                $this->sendManagerLetter($message_admin, $manager_emails);

                unset($session->item);

                if (isset($zakaz_data['PAYMENT']) && ($zakaz_data['PAYMENT'] == $wmz_payment)) {
                    $this->_redirect('/cart/payment/');
                } else {

                    $sesVcene = new Zend_Session_Namespace('metriks');

                    $g['EMAIL'] = $this->order['EMAIL'];
                    $g['TOTAL_PRICE'] = $total_price;
                    $g['ZAKAZ_ID'] = $this->order['ZAKAZ_ID'];
                    $g['ITEMS'] = implode(', ', $vCeneIDItems);

                    $sesVcene->vcene = array(
                        'EMAIL' => $this->order['EMAIL'],
                        'TOTAL_PRICE' => $total_price,
                        'ZAKAZ_ID' => $this->order['ZAKAZ_ID'],
                        'ITEMS' => implode(', ', $vCeneIDItems)
                    );
                }
            }
        }

        $this->_redirect('/cart/thanks/');
    }

    public function thanksAction()
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/cart/thanks/');
        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);

        $this->getDocPath($doc_id);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        // Собираем меню каталога
        $sesVcene = new Zend_Session_Namespace('metriks');

        if ($sesVcene->vcene) {
            $this->getvCene($sesVcene->vcene);
            // Удаляем из сессии чтобы при обновлении страницы счётчик не срабатывал по сто раз
            unset($sesVcene->vcene);
        }
    }

    public function successAction()
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/cart/success_payment/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);

        $this->getDocPath($doc_id);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();
    }

    public function failAction()
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/cart/fail_payment/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;

        $this->openData($o_data);

        $this->getDocPath($doc_id);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();
    }

    public function paymentresultAction()
    {
        $Cart = new models_Cart();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $statusid_payment_wm_succes = $this->getSettingValue('statusid_payment_wm_succes') ? $this->getSettingValue('statusid_payment_wm_succes') : '';
            $statusid_payment_wm_fail = $this->getSettingValue('statusid_payment_wm_fail') ? $this->getSettingValue('statusid_payment_wm_fail') : '';
            $purse_wmz = $this->getSettingValue('purse_wmz') ? $this->getSettingValue('purse_wmz') : '';

            $LMI_PAYEE_PURSE = $request->getPost('LMI_PAYEE_PURSE');
            $LMI_PAYMENT_AMOUNT = $request->getPost('LMI_PAYMENT_AMOUNT');
            $LMI_PAYMENT_NO = $request->getPost('LMI_PAYMENT_NO');
            $LMI_MODE = $request->getPost('LMI_MODE');
            $LMI_SYS_INVS_NO = $request->getPost('LMI_SYS_INVS_NO');
            $LMI_SYS_TRANS_NO = $request->getPost('LMI_SYS_TRANS_NO');
            $LMI_SYS_TRANS_DATE = $request->getPost('LMI_SYS_TRANS_DATE');
            $LMI_PAYER_PURSE = $request->getPost('LMI_PAYER_PURSE');
            $LMI_PAYER_WM = $request->getPost('LMI_PAYER_WM');

            $LMI_HASH = $request->getPost('LMI_HASH');

            //      $filename = 'pay_test.txt';
            //      if(is_file($filename)) unlink($filename);
            //      $handle = fopen($filename, 'a');
            //      foreach($this->payment['LMI_PAYER_WM'] as $k=>$view){
            //        $somecontent = $k.'='.$view."\r\n";
            //        fwrite($handle, $somecontent);
            //      }

            if (!empty($LMI_PAYMENT_NO) && !empty($LMI_HASH)) {
                if (($LMI_PAYEE_PURSE == $purse_wmz)) {
                    $res = $LMI_PAYEE_PURSE . $LMI_PAYMENT_AMOUNT . $LMI_PAYMENT_NO . $LMI_MODE . $LMI_SYS_INVS_NO . $LMI_SYS_TRANS_NO . $LMI_SYS_TRANS_DATE . $LMI_PAYER_PURSE . $LMI_PAYER_WM;
                    $res_ = strtoupper(md5($res));
                    if ($res_ == $LMI_HASH) {
                        $Cart->setStatusZakaz($statusid_payment_wm_succes,
                                              $LMI_PAYMENT_NO);
                    } else {
                        $Cart->setStatusZakaz($statusid_payment_wm_fail,
                                              $LMI_PAYMENT_NO);
                    }
                } else {
                    $Cart->setStatusZakaz($statusid_payment_wm_fail,
                                          $LMI_PAYMENT_NO);
                }
            }
        }

        //      fclose($handle);
    }

    public function paymentAction()
    {
        $Cart = new models_Cart();

        $purse_wmz = $this->getSettingValue('purse_wmz') ? $this->getSettingValue('purse_wmz') : '';
        $site_url = $this->getSettingValue('site_url') ? $this->getSettingValue('site_url') : '';

        $this->openData($this->currency);

        if (isset($_SESSION['ses_zakaz_id']) && !empty($_SESSION['ses_zakaz_id'])) {
            $name = $Cart->selectZakazName($_SESSION['ses_zakaz_id']);
            $zakaz_items = $Cart->selectZakaz($_SESSION['ses_zakaz_id']);
            $cost = 0;
            $currency_id = $Cart->curIDWMZ();
            if (!empty($zakaz_items)) {
                foreach ($zakaz_items as $view) {
                    $cost+=$view['PRICE'] * $view['QUANTITY'];
                }
            }

            $this->domXml->create_element('zakaz_cost', $cost, 2);
            $this->domXml->go_to_parent();
            $this->domXml->create_element('surname_err', $name, 2);
            $this->domXml->go_to_parent();
            $this->domXml->create_element('payment_no',
                                          $_SESSION['ses_zakaz_id'], 2);
            $this->domXml->go_to_parent();
        }

        $this->domXml->create_element('purse_wmz', $purse_wmz, 2);
        $this->domXml->go_to_parent();
        $this->domXml->create_element('site_url', $site_url, 2);
        $this->domXml->go_to_parent();
    }

    private function getvCene(array $paramsTracker)
    {

        $this->domXml->set_tag('page');
        $this->domXml->create_element('banner_java_scripts', '', 1);
//        $this->domXml->create_element('banner_code', '', 1);
//        'EMAIL' => $this->order['EMAIL'],
//        'TOTAL_PRICE' => $total_price,
//        'ZAKAZ_ID' => $this->order['ZAKAZ_ID'],
//        'ITEMS' => implode(', ', $vCeneIDItems)

        $traker = "<script type=\"text/javascript\">
                vcene_trusted_host = \"http://trusted.vcene.ua/\";
                vcene_account_id = \"b34e3c5796d784f35b24ba2f530a4814\";
                vcene_client_email = \"{$paramsTracker['EMAIL']}\";
                vcene_order_id = {$paramsTracker['ZAKAZ_ID']};
                vcene_order_amount = {$paramsTracker['TOTAL_PRICE']};
                vcene_order_products = [{$paramsTracker['ITEMS']}];
                </script>
                <script type=\"text/javascript\" src=\"http://trusted.vcene.ua/js/trace.js\"></script>
                ";

        $this->domXml->createElement('banner_code', $traker, 0, array(), 1);

        // Удаляем - используем обновленный DOMXml поэтому достаточно просто добавить create_element
//	$ap_helper = $this->_helper->helperLoader('AnotherPages');
//	$ap_helper->setDomXml($this->domXml);
//	$ap_helper->setXmlNode($traker, 'banner_code');
//	$this->domXml = $ap_helper->getDomXml();
    }

    private function getManagerLetter($table, $postData)
    {
        $AnotherPages = new models_AnotherPages();

        $docId = $AnotherPages->getDocByUrl('order_buy_admin');
        $messageAmin = $AnotherPages->getDocXml($docId);

        if (!empty($postData['SURNAME']))
            $messageAmin = str_replace("##surname##", $postData['SURNAME'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##surname##", '', $messageAmin);

        if (!empty($postData['_payment']))
            $messageAmin = str_replace("##payment##", $postData['_payment'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##payment##", '', $messageAmin);

        if (!empty($postData['telmob']))
            $messageAmin = str_replace("##phone##", $postData['telmob'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##phone##", '', $messageAmin);

        if (!empty($postData['info']))
            $messageAmin = str_replace("##info##", $postData['info'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##info##", '', $messageAmin);

        if (!empty($postData['name']))
            $messageAmin = str_replace("##name##", $postData['name'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##name##", '', $messageAmin);

        if (!empty($postData['address']))
            $messageAmin = str_replace("##address##", $postData['address'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##address##", '', $messageAmin);

        if (!empty($postData['email']))
            $messageAmin = str_replace("##email##", $postData['email'],
                                       $messageAmin);
        else
            $messageAmin = str_replace("##email##", '', $messageAmin);

        $messageAmin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
                . $messageAmin . $table . '</body></html>';

        return $messageAmin;
    }

    private function sendManagerLetter($message_admin, $manager_emails,
                                       $subject = 'Оформление заказа')
    {
        if (!empty($manager_emails)) {
            $email_from = $this->getSettingValue('email_from');
            $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
            preg_match_all($patrern, $email_from, $arr);

            foreach ($manager_emails as $mm) {
                $mm = trim($mm);
                if (!empty($mm)) {
                    $params['to'] = $mm;
                    $params['message'] = $message_admin;
                    $params['subject'] = $subject;
                    $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
                    $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);

                    App_Mail::send($params);
                }
            }
        }
    }

}