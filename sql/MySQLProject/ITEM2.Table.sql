﻿--
-- Описание для таблицы ITEM2
--
CREATE TABLE ITEM2 (
  ITEM_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  ATTRIBUT_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  VALUE varchar(255) DEFAULT NULL,
  PRIMARY KEY (ITEM_ID, ATTRIBUT_ID),
  INDEX idx_ITEM2_1 (ATTRIBUT_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 44402
AVG_ROW_LENGTH = 41
CHARACTER SET utf8
COLLATE utf8_general_ci;
