﻿--
-- Описание для таблицы SITE_PARAM
--
CREATE TABLE SITE_PARAM (
  PARAM_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  SYSNAME varchar(255) DEFAULT NULL,
  NAME varchar(255) DEFAULT NULL,
  VALUE varchar(255) DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (PARAM_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;
