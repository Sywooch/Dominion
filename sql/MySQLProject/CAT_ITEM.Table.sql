﻿--
-- Описание для таблицы CAT_ITEM
--
CREATE TABLE CAT_ITEM (
  CATALOGUE_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  ITEM_ID int(12) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (CATALOGUE_ID, ITEM_ID)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;
