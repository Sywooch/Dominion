﻿--
-- Описание для таблицы MANAGERS
--
CREATE TABLE MANAGERS (
  MANAGER_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  NAME varchar(255) DEFAULT NULL,
  EMAIL varchar(255) DEFAULT NULL,
  EMAIL_STATUS int(1) UNSIGNED DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (MANAGER_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 56
CHARACTER SET utf8
COLLATE utf8_general_ci;