<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Change table type for CATALOGUE_BRAND_VIEW
 * and set foreign key to CATALOGUE
 *
 * @package DominionMigrations
 */
class Version20140213184114 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->_addSql('ALTER TABLE CATALOGUE_BRAND_VIEW ENGINE = INNODB');

        $this->_addSql(
            "DELETE FROM CATALOGUE_BRAND_VIEW  WHERE CATALOGUE_ID NOT IN (
                            SELECT C.CATALOGUE_ID FROM CATALOGUE C
                      )"
        );

        $this->_addSql("
            ALTER TABLE CATALOGUE_BRAND_VIEW
              ADD CONSTRAINT  FK_CATALOGUE_BRAND_VIEW_CATALOGUE FOREIGN KEY(CATALOGUE_ID)
              REFERENCES CATALOGUE(CATALOGUE_ID) ON DELETE CASCADE ON UPDATE CASCADE"
        );

    }

    public function down(Schema $schema)
    {

        $this->_addSql("
            ALTER TABLE CATALOGUE_BRAND_VIEW
              DROP FOREIGN KEY FK_CATALOGUE_BRAND_VIEW_CATALOGUE"
        );

        $this->_addSql("ALTER TABLE CATALOGUE_BRAND_VIEW
          DROP INDEX FK_CATALOGUE_BRAND_VIEW_CATALOGUE");

        $this->_addSql('ALTER TABLE CATALOGUE_BRAND_VIEW ENGINE = MYISAM');

    }
}