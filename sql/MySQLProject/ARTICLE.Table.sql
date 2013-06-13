﻿--
-- Описание для таблицы ARTICLE
--
CREATE TABLE ARTICLE (
  ARTICLE_ID int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  ARTICLE_GROUP_ID int(12) UNSIGNED DEFAULT NULL,
  DATA datetime DEFAULT NULL,
  NAME varchar(255) DEFAULT NULL,
  CATNAME varchar(255) NOT NULL,
  DESCRIPTION text DEFAULT NULL,
  IMAGE1 varchar(255) NOT NULL DEFAULT '',
  URL varchar(255) DEFAULT NULL,
  STATUS int(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (ARTICLE_ID),
  INDEX idx_ARTICLE_1 (DATA),
  INDEX idx_ARTICLE_2 (ARTICLE_GROUP_ID)
)
ENGINE = MYISAM
AUTO_INCREMENT = 27
AVG_ROW_LENGTH = 864
CHARACTER SET utf8
COLLATE utf8_general_ci;
