﻿--
-- Описание для таблицы CMF_BUG
--
CREATE TABLE CMF_BUG (
  CMF_BUG_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  CMF_USER_ID int(12) UNSIGNED DEFAULT NULL,
  DATA datetime DEFAULT NULL,
  URL varchar(255) DEFAULT NULL,
  DESCRIPTION text DEFAULT NULL,
  STATUS int(12) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (CMF_BUG_ID),
  INDEX idx_CMF_BUG_1 (DATA)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;