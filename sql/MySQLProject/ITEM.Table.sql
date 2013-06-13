﻿--
-- Описание для таблицы ITEM
--
CREATE TABLE ITEM (
  ITEM_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  CATALOGUE_ID int(12) UNSIGNED NOT NULL,
  TYPENAME varchar(255) DEFAULT NULL,
  BRAND_ID int(12) UNSIGNED DEFAULT NULL,
  DISCOUNT_ID int(12) UNSIGNED DEFAULT NULL,
  WARRANTY_ID int(12) UNSIGNED NOT NULL,
  DELIVERY_ID int(12) UNSIGNED NOT NULL,
  CREDIT_ID int(12) UNSIGNED NOT NULL,
  NAME varchar(255) DEFAULT NULL,
  CATNAME varchar(255) NOT NULL,
  ARTICLE varchar(255) NOT NULL,
  CURRENCY_ID int(12) UNSIGNED DEFAULT NULL,
  PRICE double DEFAULT NULL,
  PRICE1 double DEFAULT NULL,
  IMAGE1 varchar(50) DEFAULT NULL,
  PURCHASE_PRICE double NOT NULL,
  IMAGE2 varchar(50) DEFAULT NULL,
  IMAGE3 varchar(50) DEFAULT NULL,
  NEED_RESIZE int(1) UNSIGNED NOT NULL,
  BASE_IMAGE varchar(255) NOT NULL,
  DESCRIPTION text DEFAULT NULL,
  SEO_BOTTOM text DEFAULT NULL,
  YANDEX_XML int(1) UNSIGNED DEFAULT NULL,
  TITLE varchar(255) NOT NULL,
  DESC_META text NOT NULL,
  CC_XML text DEFAULT NULL,
  KEYWORD_META text NOT NULL,
  DATE_INSERT date DEFAULT NULL,
  IS_ACTION tinyint(1) UNSIGNED NOT NULL,
  IS_CASHED tinyint(1) NOT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (ITEM_ID),
  INDEX idx_ITEM_1 (CATALOGUE_ID),
  INDEX idx_ITEM_2 (BRAND_ID),
  UNIQUE INDEX idx_ITEM_3 (ARTICLE),
  CONSTRAINT FK_ITEM_CATALOGUE_CATALOGUE_ID FOREIGN KEY (CATALOGUE_ID)
  REFERENCES CATALOGUE (CATALOGUE_ID) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 44734
AVG_ROW_LENGTH = 2218
CHARACTER SET utf8
COLLATE utf8_general_ci;
