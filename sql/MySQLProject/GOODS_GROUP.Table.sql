﻿--
-- Описание для таблицы GOODS_GROUP
--
CREATE TABLE GOODS_GROUP (
  GOODS_GROUP_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  TITLE varchar(255) DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  IMPORT_IDENT varchar(255) DEFAULT NULL,
  IMPORT_IDENT_XML varchar(100) DEFAULT NULL,
  IN_FRONT int(1) UNSIGNED DEFAULT NULL,
  IN_CATALOG int(1) UNSIGNED NOT NULL,
  ORDERING int(12) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (GOODS_GROUP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 56
CHARACTER SET utf8
COLLATE utf8_general_ci;
