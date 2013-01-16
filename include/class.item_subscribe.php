<?php

class item_subscribe
{

    private $tr = array(
        "Г" => "G", "Ё" => "YO", "Є" => "E", "Ю" => "YI", "Я" => "I",
        "и" => "i", "г" => "g", "ё" => "yo", "№" => "#", "є" => "e",
        "ї" => "yi", "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
        "Д" => "D", "Е" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I",
        "Й" => "J", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
        "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
        "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
        "Ш" => "SH", "Щ" => "SCH", "Ъ" => "'", "Ы" => "YI", "Ь" => "",
        "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
        "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
        "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "'",
        "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", " " => '-',
        "…" => "-", "і" => "i", "®" => "-", "І" => "I",
        "«" => "-", "»" => "-", "(" => "", ")" => ""
    );

    public function run()
    {
        Zend_Loader::loadClass('models_Item');
        Zend_Loader::loadClass('models_SystemSets');
        Zend_Loader::loadClass('models_AnotherPages');

        $Item = new models_Item();

        $items = $Item->getReservedItems();

        $items_id = array();

        $result = array();
        if (!empty($items)) {
            foreach ($items as $itm) {
                $items_id[] = $itm['ITEM_ID'];

                if (!isset($result[$itm['EMAIL']][$itm['ITEM_ID']])) {
                    $item_name = $itm['BRAND_NAME'] . ' ' . $itm['NAME'];
                    if (!empty($itm['TYPENAME']))
                        $item_name = $itm['TYPENAME'] . ' ' . $item_name;


                    $href = HTTP_HOST . $itm['CATALOGUE_REALCATNAME'] . '/item/' . $itm['ITEM_ID'] . '-' . $itm['CATNAME'];

                    $result[$itm['EMAIL']][$itm['ITEM_ID']]['name'] = $item_name;
                    $result[$itm['EMAIL']][$itm['ITEM_ID']]['href'] = $href;
                }
            }
        }

        if (!empty($result)) {
            $this->createMessage($result);
        }

        if (!empty($items_id)) {
            $items_id = array_unique($items_id);
            $this->updateReservedData($items_id);
        }
    }

    private function createMessage($result)
    {
        $SystemSets = new models_SystemSets();

        $email_from = $SystemSets->getSettingValue('email_from');

        $patrern = '/(.*)<?([a-zA-Z0-9\-\_]+\@[a-zA-Z0-9\-\_]+(\.[a-zA-Z0-9]+?)+?)>?/U';
        preg_match_all($patrern, $email_from, $arr);

        $params['mailerFrom'] = empty($arr[2][0]) ? '' : trim($arr[2][0]);
        $params['mailerFromName'] = empty($arr[1][0]) ? '' : trim($arr[1][0]);
        $params['subject'] = $SystemSets->getSettingValue('reserved_email_subject');

        foreach ($result as $email => $items) {
            $params['to'] = $email;
            $params['message'] = $this->getMessage($items);

            $this->send($params);
        }
    }

    private function updateReservedData($items_id)
    {
        $_items_id = implode(',', $items_id);

        $Item = new models_Item();
        $Item->updateReservedData($_items_id);
    }

    private function getMessage($items)
    {
        $AnotherPages = new models_AnotherPages();

        $doc_id = $AnotherPages->getDocId('reserved_email');
        $message_admin = $AnotherPages->getDocXml($doc_id, 0);

        $table = "<table cellspacing='0' cellpadding='2' border='1'>
              <tbody>
                <tr>
                    <th>Наименование товара</th>
                </tr>";

        if (!empty($items)) {
            foreach ($items as $itm) {
                $table.="<tr>
            <th><a href='{$itm['href']}'>" . $itm['name'] . "</a></th>
          </tr>";
            }

            $message_admin = str_replace('##table##', $table, $message_admin);

            $message_admin = '<html><head><meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body>'
                    . $message_admin . '</body></html>';

            return $message_admin;
        }

        return '';
    }

    /**
     * @params to - send email
     *         message - mail message
     *         subject - mail subject
     *         attach - link file source
     *         name - file name
     *         attach_type - file type
     */
    private function send($params)
    {
        Zend_Loader::loadClass('Zend_Mail');

        $mailer = new Zend_Mail('utf-8');

        $mailer->setFrom($params['mailerFrom'], $params['mailerFromName']);
        $mailer->setSubject($params['subject']);
        $mailer->addTo($params['to']);
        $mailer->setBodyHtml($params['message'], 'UTF-8',
                             Zend_Mime::ENCODING_BASE64);

        if (!empty($params['attach'])) {
            $logo = new Zend_Mime_part(file_get_contents($params['attach']));
            $logo->type = $params['attach_type'];
            $logo->disposition = Zend_Mime::DISPOSITION_INLINE;
            $logo->encoding = Zend_Mime::ENCODING_BASE64;
            $logo->filename = $params['name'];

            $at = $mailer->addAttachment($logo);
        }
        try {
            $mailer->send();
        } catch (Exception $ex) {
            echo "Ошибка отправки электронного письма на ящик " . $params['to'];
            exit;
        }
    }

    private function translit($cyr_str)
    {
        return strtr($cyr_str, $this->tr);
    }

}
