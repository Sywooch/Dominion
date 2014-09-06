<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class Version20140907002515 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema->getTable("ANOTHER_PAGES")->addColumn(
            "SHOW_NEAR_CATALOGUE_MENU",
            Type::INTEGER
        )->setDefault(0)
            ->setNotnull(false)
            ->setScale(1);
    }

    public function down(Schema $schema)
    {
        $schema->getTable("ANOTHER_PAGES")
            ->dropColumn("SHOW_NEAR_CATALOGUE_MENU");
    }
}