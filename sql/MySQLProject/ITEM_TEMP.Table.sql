﻿--
-- Описание для таблицы ITEM_TEMP
--
CREATE TABLE ITEM_TEMP (
  ITEM_TEMP_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  ITEM_ID int(12) UNSIGNED NOT NULL,
  ATTRIBUT_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  VALUE text DEFAULT NULL,
  PRIMARY KEY (ITEM_TEMP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 488336
AVG_ROW_LENGTH = 45
CHARACTER SET utf8
COLLATE utf8_general_ci;
