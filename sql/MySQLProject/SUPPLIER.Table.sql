﻿--
-- Описание для таблицы SUPPLIER
--
CREATE TABLE SUPPLIER (
  SUPPLIER_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  PRIMARY KEY (SUPPLIER_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 24
AVG_ROW_LENGTH = 20
CHARACTER SET cp1251
COLLATE cp1251_ukrainian_ci;