﻿--
-- Описание для таблицы ITEM_ITEM
--
CREATE TABLE ITEM_ITEM (
  CATALOGUE_ID int(12) UNSIGNED DEFAULT NULL,
  ITEM_ID int(12) UNSIGNED DEFAULT NULL,
  ITEM_ITEM_ID int(12) UNSIGNED NOT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  INDEX ITEM_ID (ITEM_ID, ITEM_ITEM_ID)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 17
CHARACTER SET cp1251
COLLATE cp1251_ukrainian_ci;
