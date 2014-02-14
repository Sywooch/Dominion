<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20140214134914 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->_addSql('ALTER TABLE ITEM ADD INDEX IDX_ITEM_STATUS (STATUS)');

        $this->_addSql('ALTER TABLE ITEM ADD INDEX IDX_ITEM_CATALOGUE_BRAND_STATUS (CATALOGUE_ID, BRAND_ID, STATUS)');

    }

    public function down(Schema $schema)
    {
        $this->_addSql('ALTER TABLE ITEM DROP INDEX IDX_ITEM_STATUS');

        $this->_addSql('ALTER TABLE ITEM DROP INDEX IDX_ITEM_CATALOGUE_BRAND_STATUS');
    }
}