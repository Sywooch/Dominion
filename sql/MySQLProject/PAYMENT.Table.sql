﻿--
-- Описание для таблицы PAYMENT
--
CREATE TABLE PAYMENT (
  PAYMENT_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  ORDERING int(12) UNSIGNED DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (PAYMENT_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 48
CHARACTER SET utf8
COLLATE utf8_general_ci;