﻿--
-- Описание для таблицы ITEM0
--
CREATE TABLE ITEM0 (
  ITEM_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  ATTRIBUT_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  VALUE int(12) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (ITEM_ID, ATTRIBUT_ID),
  INDEX idx_ITEM0_1 (ATTRIBUT_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 44402
AVG_ROW_LENGTH = 13
CHARACTER SET utf8
COLLATE utf8_general_ci;
