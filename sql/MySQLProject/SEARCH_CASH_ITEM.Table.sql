﻿--
-- Описание для таблицы SEARCH_CASH_ITEM
--
CREATE TABLE SEARCH_CASH_ITEM (
  ITEM_ID int(12) UNSIGNED NOT NULL,
  CATALOGUE_ID int(12) UNSIGNED NOT NULL,
  BRAND_ID int(12) UNSIGNED DEFAULT NULL,
  SEARCH_CASH text NOT NULL,
  PRIMARY KEY (ITEM_ID),
  INDEX BRAND_ID (BRAND_ID),
  INDEX CATALOGUE_ID (CATALOGUE_ID),
  FULLTEXT INDEX SEARCH_CASH (SEARCH_CASH)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 80
CHARACTER SET utf8
COLLATE utf8_general_ci;
