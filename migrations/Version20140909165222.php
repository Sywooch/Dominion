<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20140909165222 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->_addSql('ALTER TABLE ATTRIBUT DROP FOREIGN KEY FK_ATTRIBUT_ATTRIBUT_GROUP_ATTRIBUT_GROUP_ID');

        $this->_addSql('ALTER TABLE ATTRIBUT
                        ADD CONSTRAINT FK_ATTRIBUT_ATTRIBUT_GROUP_ATTRIBUT_GROUP_ID FOREIGN KEY (ATTRIBUT_GROUP_ID)
                        REFERENCES ATTRIBUT_GROUP(ATTRIBUT_GROUP_ID) ON DELETE CASCADE ON UPDATE RESTRICT');

    }

    public function down(Schema $schema)
    {
        $this->_addSql('ALTER TABLE ATTRIBUT DROP FOREIGN KEY FK_ATTRIBUT_ATTRIBUT_GROUP_ATTRIBUT_GROUP_ID');

        $this->_addSql('ALTER TABLE ATTRIBUT
                        ADD CONSTRAINT FK_ATTRIBUT_ATTRIBUT_GROUP_ATTRIBUT_GROUP_ID FOREIGN KEY (ATTRIBUT_GROUP_ID)
                        REFERENCES ATTRIBUT_GROUP(ATTRIBUT_GROUP_ID) ON DELETE RESTRICT ON UPDATE RESTRICT');
    }
}