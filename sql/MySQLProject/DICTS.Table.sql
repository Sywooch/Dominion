﻿--
-- Описание для таблицы DICTS
--
CREATE TABLE DICTS (
  DICTS_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  DICTS_GROUP_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  NAME varchar(255) DEFAULT NULL,
  DESCRIPTION text DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (DICTS_ID),
  INDEX idx_DICTS_1 (DICTS_GROUP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;
