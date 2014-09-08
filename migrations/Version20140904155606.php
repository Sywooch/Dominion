<?php

namespace DominionMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
  Doctrine\DBAL\Schema\Schema;

class Version20140904155606 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema->getTable('BRAND')->getColumn('IMAGE')->setNotnull(false);
        $schema->getTable('BRAND')->getColumn('IN_INDEX')->setNotnull(false);
        $schema->getTable('BRAND')->getColumn('IN_ALL_PAGES')->setNotnull(false);
    }

    public function down(Schema $schema)
    {
        $schema->getTable('BRAND')->getColumn('IMAGE')->setNotnull(true);
        $schema->getTable('BRAND')->getColumn('IN_INDEX')->setNotnull(true);
        $schema->getTable('BRAND')->getColumn('IN_ALL_PAGES')->setNotnull(true);
    }
}