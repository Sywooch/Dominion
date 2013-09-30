<?php
class models_Cart extends ZendDBEntity
{
    protected $_name = 'ITEM';

    public function getPaymentInfo($id)
    {
        $sql = "SELECT *
           FROM PAYMENT
           WHERE PAYMENT_ID = ?";

        return $this->_db->fetchRow($sql, (int) $id);
    }

    public function selectZakazName($id)
    {
        $sql = "SELECT NAME
           FROM ZAKAZ
           WHERE ZAKAZ_ID = {$id}";

        return $this->_db->fetchOne($sql);
    }

    public function selectZakaz($id)
    {
        $sql = "SELECT *
           FROM ZAKAZ_ITEM
           WHERE ZAKAZ_ID = {$id}";

        return $this->_db->fetchAll($sql);
    }

    public function curIDWMZ()
    {
        $sql = "SELECT CURRENCY_ID
           FROM CURRENCY
           WHERE SYSTEM_NAME = 'WMZ'";

        return $this->_db->fetchOne($sql);
    }

    public function setStatusZakaz($status, $id)
    {
        $sql = "UPDATE ZAKAZ
           SET STATUS = {$status}
           WHERE ZAKAZ_ID = {$id}";

        $this->_db->query($sql);
    }

    public function getUserDiscountId($user_id)
    {
        $sql = "SELECT SHOPUSER_DISCOUNTS_ID
            FROM SHOPUSER
            WHERE USER_ID = {$user_id}";

        return $this->_db->fetchOne($sql);
    }

    public function getDiscountData($discounts_id)
    {
        $sql = "SELECT *
            FROM SHOPUSER_DISCOUNTS
            WHERE SHOPUSER_DISCOUNTS_ID = {$discounts_id}";

        return $this->_db->fetchRow($sql);
    }

    public function getNextDiscountId($ordering)
    {
        $sql = "SELECT SHOPUSER_DISCOUNTS_ID
            FROM SHOPUSER_DISCOUNTS
            WHERE ORDERING > {$ordering}
            ORDER BY ORDERING
            LIMIT 1";

        return $this->_db->fetchRow($sql);
    }

    public function getUserOrderSumm($id)
    {
        $sql = "SELECT sum(ZI.COST)
          FROM ZAKAZ_ITEM ZI
          INNER JOIN ZAKAZ Z ON (Z.ZAKAZ_ID = ZI.ZAKAZ_ID)
          WHERE Z.USER_ID = ?
          AND Z.STATUS = 3 ";

        return $this->_db->fetchOne($sql, $id);
    }

}
