<?php

class RegisterController extends App_Controller_Frontend_Action
{

    function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $Registration = new models_Registration();

            $postData = $request->getPost();

            if (!empty($postData['captcha'])) {
                if ($postData['captcha'] != $_SESSION['biz_captcha']) {
                    return false;
                }
            }

            $insert_data['NAME'] = $postData['name'];
            $insert_data['PRIVATEINFO'] = $postData['comment'];
            $insert_data['PASSWORD'] = $postData['passwd'];
            $insert_data['TELMOB'] = $postData['phone'];
            $insert_data['EMAIL'] = $postData['email'];
            $insert_data['REGDATA'] = date('Y-m-d H:i:s');
            $insert_data['STATUS'] = 1;

            $Registration->insertRegData($insert_data);

            $user_message = $this->getUserRegisterLetter($insert_data);
            $email_from = $this->getSettingValue('email_from');
            $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
            preg_match_all($patrern, $email_from, $arr);

            $params['to'] = $postData['email'];
            $params['message'] = $user_message;
            $params['subject'] = 'Спасибо за регистрацию';
            $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
            $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);
            App_Mail::send($params);

            $message = $this->getManagerRegisterLetter($insert_data);

            $manager_emails = $this->getSettingValue('manager_emails');
            $manager_emails_arr = explode(";", $manager_emails);

            $this->sendManagerLetter($message, $manager_emails_arr, $subject = 'Регистрация пользователя');

            $this->_redirect('/register/complete/');
        }

        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/register/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;
        $o_data['institution_id'] = 0;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $this->getDocPath($doc_id);
    }

    public function completeAction()
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/register/complete/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;
        $o_data['institution_id'] = 0;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $this->getDocPath($doc_id);
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();

        $this->_redirect('/');
    }

    public function accountAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
            $this->_redirect('/');

        $AnotherPages = new models_AnotherPages();
        $Registration = new models_Registration();

        $user_data = Zend_Auth::getInstance()->getIdentity();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $insert_data['NAME'] = $postData['name'];
            $insert_data['PRIVATEINFO'] = $postData['comment'];
            $insert_data['TELMOB'] = $postData['phone'];
            $insert_data['EMAIL'] = $postData['email'];
            $insert_data['REGDATA'] = date('Y-m-d H:i:s');
            $insert_data['STATUS'] = 1;

            if (!empty($postData['passwd'])) $insert_data['PASSWORD'] = $postData['passwd'];

            $_user_data = array('user_name' => $insert_data['NAME'],
                'user_id' => $user_data['user_id'],
                'user_phone' => $insert_data['TELMOB'],
                'user_email' => $insert_data['EMAIL'],
                array('Login succesful'));

            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($_user_data);

            $Registration->updateRegData($insert_data, $user_data['user_id']);

            $this->_redirect('/register/account/');
        }

        $doc_id = $AnotherPages->getDocByUrl('/register/account/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;
        $o_data['institution_id'] = 0;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $ap_helper = $this->_helper->helperLoader('Register');
        $ap_helper->setModel($Registration);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getMyData($user_data['user_id']);
        $this->domXml = $ap_helper->getDomXml();

        $this->getDocPath($doc_id);
    }

    public function myordersAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
            $this->_redirect('/');

        $AnotherPages = new models_AnotherPages();
        $Registration = new models_Registration();
        $Item = new models_Item();

        $doc_id = $AnotherPages->getDocByUrl('/register/myorders/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;
        $o_data['institution_id'] = 0;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $user_data = Zend_Auth::getInstance()->getIdentity();

        $params['Item'] = $Item;
        $params['currency'] = $this->currency;

        $ap_helper = $this->_helper->helperLoader('Register', $params);
        $ap_helper->setModel($Registration);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getMyOrders($user_data['user_id']);
        $this->domXml = $ap_helper->getDomXml();

        $this->getDocPath($doc_id);
    }

    public function mybonusAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
            $this->redirect('/');

        $AnotherPages = new models_AnotherPages();
        $Cart = new models_Cart();

        $doc_id = $AnotherPages->getDocByUrl('/register/mybonus/');

        $o_data['id'] = 0;
        $o_data['currency'] = $this->currency;
        $o_data['institution_id'] = 0;

        $this->openData($o_data);

        $ap_helper = $this->_helper->helperLoader('AnotherPages');
        $ap_helper->setModel($AnotherPages);
        $ap_helper->setDomXml($this->domXml);
        $ap_helper->getDocInfo($doc_id);
        $this->domXml = $ap_helper->getDomXml();

        $user_data = Zend_Auth::getInstance()->getIdentity();

        $rg_helper = $this->_helper->helperLoader('Register');
        $rg_helper->setModel($Cart);
        $rg_helper->setDomXml($this->domXml);
        $rg_helper->getMybonus($user_data);
        $this->domXml = $rg_helper->getDomXml();

        $this->getDocPath($doc_id);
    }

    private function getUserRegisterLetter($data)
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/register_user_email/');
        $message_admin = $AnotherPages->getDocXml($doc_id, 0);

        $replace = array('##name##' => $data['NAME']
        , '##phone##' => $data['TELMOB']
        , '##email##' => $data['EMAIL']
        , '##password##' => $data['PASSWORD']
        , '##comment##' => $data['PRIVATEINFO']
        );

        $message_admin = strtr($message_admin, $replace);

        $message_admin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
            . $message_admin . '</body></html>';

        return $message_admin;
    }

    private function getManagerRegisterLetter($data)
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocByUrl('/register_email/');
        $message_admin = $AnotherPages->getDocXml($doc_id, 0);

        $replace = array('##name##' => $data['NAME']
        , '##phone##' => $data['TELMOB']
        , '##email##' => $data['EMAIL']
        , '##comment##' => $data['PRIVATEINFO']
        );

        $message_admin = strtr($message_admin, $replace);

        $message_admin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
            . $message_admin . '</body></html>';

        return $message_admin;
    }

    private function getManagerLetter($table, $postData)
    {
        $message_admin = $this->Textes->getTextes('pismo-oformleniya-zakaza');

        if (!empty($postData['name'])) $message_admin = str_replace("##name##", $postData['name'], $message_admin);
        else $message_admin = str_replace("##name##", '', $message_admin);

        if (!empty($postData['phone'])) $message_admin = str_replace("##phone##", $postData['phone'], $message_admin);
        else $message_admin = str_replace("##phone##", '', $message_admin);

        if (!empty($postData['email'])) $message_admin = str_replace("##email##", $postData['email'], $message_admin);
        else $message_admin = str_replace("##email##", '', $message_admin);

        if (!empty($postData['address'])) $message_admin = str_replace("##address##", $postData['address'], $message_admin);
        else $message_admin = str_replace("##address##", '', $message_admin);

        if (!empty($postData['dopsveden'])) $message_admin = str_replace("##dopsveden##", $postData['dopsveden'], $message_admin);
        else $message_admin = str_replace("##dopsveden##", '', $message_admin);

        $message_admin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
            . $message_admin . $table . '</body></html>';

        return $message_admin;
    }

    private function sendManagerLetter($message_admin, $manager_emails, $subject = 'Оформление заказа')
    {
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