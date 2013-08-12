<?php
class models_Registration extends ZendDBEntity
{
    protected $_name = 'SHOPUSER';

    public function insertRegData($data)
    {
        $this->_db->insert('SHOPUSER', $data);
    }

    public function updateRegData($data, $uid)
    {
        $this->_db->update('SHOPUSER', $data, 'USER_ID=' . $uid);
    }

    public function resetUsersDiscount()
    {
        $sql = "UPDATE SHOPUSER
          SET SHOPUSER_DISCOUNTS_ID = 0";

        $this->_db->query($sql);
    }

    public function checkLogin($login)
    {
        $sql = "SELECT *
          FROM SHOPUSER
          WHERE EMAIL = '{$login}'";

        return $this->_db->fetchRow($sql);
    }

    public function sigin($login, $passwd)
    {
        $sql = "SELECT *
          FROM SHOPUSER
          WHERE EMAIL = '{$login}'
            AND PASSWORD = '{$passwd}'
            AND STATUS=1";

        return $this->_db->fetchRow($sql);
    }

    public function sigin_id($id)
    {
        $sql = "SELECT *
          FROM SHOPUSER
          WHERE USER_ID = {$id}";

        return $this->_db->fetchRow($sql);
    }

    public function getShopuserDiscountsId($id)
    {
        $sql = "SELECT SHOPUSER_DISCOUNTS_ID
          FROM SHOPUSER
          WHERE USER_ID = {$id}";

        return $this->_db->fetchOne($sql);
    }

    public function userData($id)
    {
        $sql = "SELECT SURNAME
               , NAME
               , PRIVATEINFO
               , TELMOB
               , TELMOBT1
               , EMAIL
          FROM SHOPUSER
          WHERE USER_ID = {$id}";

        return $this->_db->fetchRow($sql);
    }

    public function check_email($email)
    {
        $sql = "SELECT EMAIL
               , PASSWORD
          FROM SHOPUSER
          WHERE EMAIL = '{$email}'";

        return $this->_db->fetchRow($sql);
    }

    public function getOrdersCount($uid)
    {
        $sql = "SELECT count(*)
          FROM ZAKAZ
          WHERE USER_ID = {$uid}";

        return $this->_db->fetchOne($sql);
    }

    public function getOrders($uid)
    {
        $sql = "SELECT Z.*
               , ZS.NAME AS ZS_NAME
               , ZS.COLOR AS ZS_COLOR
               , DATE_FORMAT(DATA,'%d.%m.%Y') AS date
          FROM ZAKAZ Z
          LEFT JOIN ZAKAZSTATUS ZS ON (ZS.ZAKAZSTATUS_ID = Z.STATUS)
          WHERE Z.USER_ID = {$uid}
          ORDER BY Z.DATA";

        return $this->_db->fetchAll($sql);
    }

    public function getZakazItems($id)
    {
        $sql = "SELECT ZI.*
               , I.IMAGE1
               , I.CATNAME
               , C.REALCATNAME AS CATALOGUE_REALCATNAME
               , CR.SNAME
               , B.NAME AS BRAND_NAME
          FROM ZAKAZ_ITEM ZI
          JOIN ITEM I ON (ZI.ITEM_ID = I.ITEM_ID)
          JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          JOIN CURRENCY CR ON (CR.CURRENCY_ID = ZI.CURRENCY_ID)
          JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
          WHERE ZAKAZ_ID = {$id}";

        return $this->_db->fetchAll($sql);
    }
}

?>
