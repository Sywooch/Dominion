﻿--
-- Описание для таблицы ATTRIBUT_LIST
--
CREATE TABLE ATTRIBUT_LIST (
  ATTRIBUT_LIST_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  ATTRIBUT_ID int(12) UNSIGNED DEFAULT NULL,
  NAME varchar(255) DEFAULT NULL,
  COMMENT_ text DEFAULT NULL,
  PRIMARY KEY (ATTRIBUT_LIST_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 23885
AVG_ROW_LENGTH = 33
CHARACTER SET utf8
COLLATE utf8_general_ci;
