<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class Version20140907010736 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $tableAttribut = $schema->getTable("ATTRIBUT");
        $tableAttribut->addColumn(
            "EXPAND",
            Type::INTEGER
        )->setDefault(0)
            ->setNotnull(false)
            ->setScale(1);

        $tableCatalogue = $schema->getTable("CATALOGUE");

        $tableCatalogue->addColumn(
            "EXPAND_PRICE",
            Type::INTEGER
        )->setDefault(0)
            ->setNotnull(false)
            ->setScale(1);
        $tableCatalogue->addColumn(
            "EXPAND_BRAND",
            Type::INTEGER
        )->setDefault(0)
            ->setNotnull(false)
            ->setScale(1);
    }

    public function down(Schema $schema)
    {
        $tableAttribute = $schema->getTable("ATTRIBUT");
        $tableCatalogue = $schema->getTable("CATALOGUE");

        $tableAttribute->dropColumn("EXPAND");
        $tableCatalogue->dropColumn("EXPAND_PRICE")
            ->dropColumn("EXPAND_BRAND");
    }
}