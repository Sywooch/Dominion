<?php

class App_Mail
{

    static private $emailSendersParams = array();

    static private $emailTransport = null;

    public function getTransport()
    {
        if (static::$emailTransport) {
            return static::$emailTransport;
        }

        $paramsSender = self::getEmailSenderParams();

        $mail_transport_config = array(
            'port' => $paramsSender['port'],
            'auth' => $paramsSender['auth'],
            'username' => $paramsSender['username'],
            'password' => $paramsSender['password']
        );

        static::$emailTransport = new Zend_Mail_Transport_Smtp($paramsSender['transport'], $mail_transport_config);

        return static::$emailTransport;
    }

    /*
    * @params to - send email
    *         message - mail message
    *         subject - mail subject
    *         attach - link file source
    *         name - file name
    *         attach_type - file type
    */
    static function send($params)
    {

        $paramsSender = self::getEmailSenderParams();

//        $mail_transport_config = array(
//            'port' => $paramsSender['port'],
//            'auth' => $paramsSender['auth'],
//            'username' => $paramsSender['username'],
//            'password' => $paramsSender['password']
//        );

        Zend_Loader::loadClass('Zend_Mail');

        $mailer = new Zend_Mail('utf-8');


        $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
        preg_match_all($patrern, $params['mailerFromName'], $arr);


        $mailerFrom = $paramsSender['username'];
        $mailerFromName = empty($arr[1][0]) ? '' : trim($arr[1][0]);

        $mailer->setFrom($mailerFrom, $mailerFromName);
        $mailer->setSubject($params['subject']);

        // Если $params['to'] - массив то делаем addTo для всех адресов
//        if (is_array($params['to'])) {
//            foreach ($params['to'] as $value) {
//                $mailer->addTo($params['to']);
//            }
//        } else {
        $mailer->addTo($params['to']);
//        }

        $mailer->setBodyHtml($params['message'], 'UTF-8', Zend_Mime::ENCODING_BASE64);

        if (!empty($params['attach'])) {
            $logo = new Zend_Mime_part(file_get_contents($params['attach']));
            $logo->type = $params['attach_type'];
            $logo->disposition = Zend_Mime::DISPOSITION_INLINE;
            $logo->encoding = Zend_Mime::ENCODING_BASE64;
            $logo->filename = $params['name'];

            $at = $mailer->addAttachment($logo);
        }
        try {

            $transport = self::getTransport();

//            $transport = new Zend_Mail_Transport_Smtp($paramsSender['transport'], $mail_transport_config);
            $mailer->send($transport);
        } catch (Exception $ex) {
//            echo "Ошибка отправки электронного письма на ящик " . $params['to'];
            return false;
        }

        return true;
    }

    /**
     * Setup and return singltone mailer params
     *
     * @return array
     */
    static private function  getEmailSenderParams()
    {
        if (!empty(self::$emailSendersParams)) {
            return self::$emailSendersParams;
        }

        Zend_Loader::loadClass('models_SystemSets');

        $model = new models_SystemSets();

        $params = $model->getSettingValue('EMAIL_SENDER');

        $params = explode("\n", $params);

//        array_walk($params, function (&$arrayElement) {
//            $arrayElement = trim($arrayElement);
//        });

        $paramsEmail = array();
        foreach ($params as $value) {
            $clearData = explode(':', $value);
            $clearData[0] = trim($clearData[0]);
            $paramsEmail["{$clearData[0]}"] = trim($clearData[1]);
        }


        //TODO: Написать проверку всех параметров - если хоть один не корректный - исключение
        self::$emailSendersParams = $paramsEmail;

        return self::$emailSendersParams;
    }
}
