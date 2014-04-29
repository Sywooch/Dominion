<?php

class models_Credit extends ZendDBEntity
{
    protected $_name = 'CREDIT';

    /**
     * Get Meneagers email for send
     *
     * @param $accountCode
     *
     * @return string
     */
    public function getManagerEmail($accountCode)
    {
        return $this->_db->fetchOne("SELECT EMAIL FROM CREDIT  WHERE CODE = ?", $accountCode);
    }

    /**
     * Get email template
     *
     * @param $accountCode
     *
     * @return string
     */
    public function getEmailTemplate($accountCode)
    {
        return $this->_db->fetchOne("SELECT EMAIL_TEMPLATE FROM CREDIT WHERE CODE = ?", $accountCode);
    }
}