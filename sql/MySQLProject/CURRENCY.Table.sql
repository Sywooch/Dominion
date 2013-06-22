﻿--
-- Описание для таблицы CURRENCY
--
CREATE TABLE CURRENCY (
  CURRENCY_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  SNAME varchar(255) DEFAULT NULL,
  SNAME_TYPE int(12) UNSIGNED DEFAULT NULL,
  SYSTEM_NAME varchar(255) DEFAULT NULL,
  PRICE double DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  ORDERING int(12) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (CURRENCY_ID)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 4096
CHARACTER SET utf8
COLLATE utf8_general_ci;