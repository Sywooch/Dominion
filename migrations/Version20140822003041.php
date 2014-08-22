<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class Version20140822003041 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema->getTable("CATALOGUE")->addColumn(
            "IMAGE_MENU",
            "string"
        )->setDefault("NULL")
            ->setLength(50);
    }

    public function down(Schema $schema)
    {
        $schema->getTable("CATALOGUE")->dropColumn("IMAGE_MENU");
    }
}