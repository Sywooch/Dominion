<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class Version20140907010736 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema->getTable("ATTRIBUT")->addColumn(
            "EXPAND",
            Type::INTEGER
        )->setDefault(0)
            ->setNotnull(false)
            ->setScale(1);
    }

    public function down(Schema $schema)
    {
        $schema->getTable("ATTRIBUT")->dropColumn("EXPAND");
    }
}