﻿--
-- Описание для таблицы SUBSCRIBE_CLIENT_GROUP
--
CREATE TABLE SUBSCRIBE_CLIENT_GROUP (
  SUBSCRIBE_CLIENT_GROUP_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (SUBSCRIBE_CLIENT_GROUP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 24
CHARACTER SET cp1251
COLLATE cp1251_ukrainian_ci;
