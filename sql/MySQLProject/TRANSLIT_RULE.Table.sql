﻿--
-- Описание для таблицы TRANSLIT_RULE
--
CREATE TABLE TRANSLIT_RULE (
  TRANSLIT_RULE_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  SRC varchar(255) DEFAULT NULL,
  TRANSLIT varchar(255) DEFAULT NULL,
  PRIMARY KEY (TRANSLIT_RULE_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 156
AVG_ROW_LENGTH = 26
CHARACTER SET utf8
COLLATE utf8_general_ci;
