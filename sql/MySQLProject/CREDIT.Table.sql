﻿--
-- Описание для таблицы CREDIT
--
CREATE TABLE CREDIT (
  CREDIT_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  DESCRIPTION text DEFAULT NULL,
  LONG_TEXT text NOT NULL,
  PRIMARY KEY (CREDIT_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 2908
CHARACTER SET cp1251
COLLATE cp1251_ukrainian_ci;