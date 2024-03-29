﻿--
-- Описание для таблицы SHOPUSER_DISCOUNTS_RANGE_LIST
--
CREATE TABLE SHOPUSER_DISCOUNTS_RANGE_LIST (
  SHOPUSER_DISCOUNTS_RANGE_LIST_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  SHOPUSER_DISCOUNTS_ID int(12) UNSIGNED NOT NULL,
  MIN double DEFAULT NULL,
  MAX double DEFAULT NULL,
  DISCOUNT_SUMM varchar(255) DEFAULT NULL,
  PRIMARY KEY (SHOPUSER_DISCOUNTS_RANGE_LIST_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 28
AVG_ROW_LENGTH = 33
CHARACTER SET cp1251
COLLATE cp1251_ukrainian_ci;
