<?php
class Helpers_Textes extends App_Controller_Helper_HelperAbstract
{

    public function getTextes($file_name)
    {
        $result = $this->work_model->getTextes($file_name, $this->lang_id);

        if (!empty($result)) {
            $this->setXmlNode($result, $file_name);
        }
    }

    public function getLangTextes($file_name)
    {
        $result = $this->work_model->getTextes($file_name, $this->lang_id);

        if (!empty($result)) {
            $result = trim(strip_tags($result));
            $this->setXmlNode($result, 'lang-' . $file_name);
        }
    }

}