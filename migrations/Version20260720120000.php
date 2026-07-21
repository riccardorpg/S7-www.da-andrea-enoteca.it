<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Aggiunge il prezzo ai vini della carta.
 */
final class Version20260720120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Aggiunge la colonna price a daa_wine (prezzo del vino).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE daa_wine ADD price NUMERIC(6, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE daa_wine DROP price');
    }
}
