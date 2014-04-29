<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20140214130505 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->_addSql('ALTER TABLE BRAND ENGINE = INNODB');

        $this->_addSql(
            "DELETE FROM CATALOGUE_BRAND_VIEW  WHERE BRAND_ID NOT IN (
                            SELECT B.BRAND_ID FROM BRAND B)"
        );

        $this->_addSql("
            ALTER TABLE CATALOGUE_BRAND_VIEW
              CHANGE COLUMN BRAND_ID BRAND_ID INT(12) UNSIGNED NOT NULL"
        );

        $this->_addSql("
            ALTER TABLE CATALOGUE_BRAND_VIEW
              ADD CONSTRAINT  FK_CATALOGUE_BRAND_VIEW_BRAND FOREIGN KEY(BRAND_ID)
              REFERENCES BRAND(BRAND_ID) ON DELETE CASCADE ON UPDATE CASCADE"
        );

    }

    public function down(Schema $schema)
    {

        $this->_addSql("
            ALTER TABLE CATALOGUE_BRAND_VIEW
              DROP FOREIGN KEY FK_CATALOGUE_BRAND_VIEW_BRAND"
        );

        $this->_addSql("ALTER TABLE CATALOGUE_BRAND_VIEW
          DROP INDEX FK_CATALOGUE_BRAND_VIEW_BRAND");

        $this->_addSql('ALTER TABLE BRAND ENGINE = MYISAM');
    }
}