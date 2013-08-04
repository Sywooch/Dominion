<?php

class models_Discounts extends ZendDBEntity
{
    protected $_name = 'DISCOUNTS';

    /**
     * Get ID discount bu name
     *
     * @param $discountName
     *
     * @return mixed
     */
    public function getDiscountId($discountName)
    {

        $sql = "SELECT DISCOUNT_ID
                FROM {$this->_name}
                  WHERE
                  `STATUS` = 1
                  AND
                  `NAME` = ?";

        $result = $this->_db->fetchOne($sql, $discountName);

        if (empty($result)) {
            return null;
        }

        return $result;
    }
}