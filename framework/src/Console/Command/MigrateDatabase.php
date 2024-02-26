<?php

namespace Attinge\Framework\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Types;
use Throwable;

class MigrateDatabase implements CommandInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $migrationDirectory,
    ) {}
    public string $name = 'database:migrations:migrate';
    /**
     * @throws Exception
     * @throws SchemaException
     * @throws Throwable
     */
    public function execute(array $params = []) : int
    {
        try {
            $this->createMigrationsTable();
            $this->connection->beginTransaction();

            $this->connection->commit();

            return 0;
        } catch (Throwable $throwable) {
            $this->connection->rollBack();
            throw $throwable;
        }
    }
    /**
     * @throws SchemaException
     * @throws Exception
     */
    private function createMigrationsTable() : void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist('migrations')) {
            $schema = new Schema();
            $table  = $schema->createTable('migrations');
            $table->addColumn('id', Types::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
            $table->addColumn('migration', Types::STRING);
            $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
            $table->setPrimaryKey(['id']);

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            $this->connection->executeQuery($sqlArray[0]);

            echo 'migrations table created' . PHP_EOL;
        }
    }
}