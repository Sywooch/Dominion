<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20140909165520 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->_addSql('ALTER TABLE ITEM0 DROP FOREIGN KEY FK_ITEM0_ATTRIBUT_ATTRIBUT_ID');
        $this->_addSql('ALTER TABLE ITEM0
                        ADD CONSTRAINT FK_ITEM0_ATTRIBUT_ATTRIBUT_ID FOREIGN KEY (ATTRIBUT_ID)
                        REFERENCES ATTRIBUT(ATTRIBUT_ID) ON DELETE CASCADE ON UPDATE RESTRICT');


        $this->_addSql('ALTER TABLE ITEM0 DROP FOREIGN KEY FK_ITEM0_ITEM_ITEM_ID');
        $this->_addSql('ALTER TABLE ITEM0
                        ADD CONSTRAINT FK_ITEM0_ITEM_ITEM_ID FOREIGN KEY (ITEM_ID)
                        REFERENCES ITEM(ITEM_ID) ON DELETE CASCADE ON UPDATE RESTRICT');

    }

    public function down(Schema $schema)
    {
        $this->_addSql('ALTER TABLE ITEM0 DROP FOREIGN KEY FK_ITEM0_ATTRIBUT_ATTRIBUT_ID');
        $this->_addSql('ALTER TABLE ITEM0
                        ADD CONSTRAINT FK_ITEM0_ATTRIBUT_ATTRIBUT_ID FOREIGN KEY (ATTRIBUT_ID)
                        REFERENCES ATTRIBUT(ATTRIBUT_ID) ON DELETE RESTRICT ON UPDATE RESTRICT');


        $this->_addSql('ALTER TABLE ITEM0 DROP FOREIGN KEY FK_ITEM0_ITEM_ITEM_ID');
        $this->_addSql('ALTER TABLE ITEM0
                        ADD CONSTRAINT FK_ITEM0_ITEM_ITEM_ID FOREIGN KEY (ITEM_ID)
                        REFERENCES ITEM(ITEM_ID) ON DELETE RESTRICT ON UPDATE RESTRICT');
    }
}