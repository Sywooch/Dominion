﻿--
-- Описание для таблицы ATTRIBUT_GROUP
--
CREATE TABLE ATTRIBUT_GROUP (
  ATTRIBUT_GROUP_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  ORDERING int(12) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (ATTRIBUT_GROUP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 202
AVG_ROW_LENGTH = 47
CHARACTER SET utf8
COLLATE utf8_general_ci;
