<?php

class App_Mail
{

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
        //FIXME: Это мега костыль! Исправить! Вынести в настройки сайта
        $mail_transport_config = array(
            'port' => 25,
            'auth' => 'login',
            'username' => 'noreply@7560000.com.ua',
            'password' => ',fhctkjyf'
        );

        Zend_Loader::loadClass('Zend_Mail');

        $mailer = new Zend_Mail('utf-8');


        $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
        preg_match_all($patrern, $params['mailerFromName'], $arr);


        $mailerFrom = $mail_transport_config['username'];
        $mailerFromName = empty($arr[1][0]) ? '' : trim($arr[1][0]);

        $mailer->setFrom($mailerFrom, $mailerFromName);
        $mailer->setSubject($params['subject']);
        $mailer->addTo($params['to']);
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


            $transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $mail_transport_config);
            $mailer->send($transport);
        } catch (Exception $ex) {
            echo "Ошибка отправки электронного письма на ящик " . $params['to'];
//            exit;
        }
    }
}
