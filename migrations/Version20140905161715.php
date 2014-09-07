<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
  Doctrine\DBAL\Schema\Schema;

class Version20140905161715 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->_addSql('ALTER TABLE ATTRIBUT_GROUP ENGINE = INNODB');

        $this->_addSql('ALTER TABLE ATTRIBUT CHANGE COLUMN ATTRIBUT_GROUP_ID ATTRIBUT_GROUP_ID INT(12) UNSIGNED NOT NULL');

        $this->_addSql('ALTER TABLE ATTRIBUT
                          ADD CONSTRAINT FK_ATTRIBUT_ATTRIBUT_GROUP_ATTRIBUT_GROUP_ID FOREIGN KEY (ATTRIBUT_GROUP_ID)
                            REFERENCES ATTRIBUT_GROUP(ATTRIBUT_GROUP_ID) ON DELETE RESTRICT ON UPDATE RESTRICT');


    }

    public function down(Schema $schema)
    {
        $this->_addSql('ALTER TABLE ATTRIBUT_GROUP ENGINE = MYISAM');
    }
}