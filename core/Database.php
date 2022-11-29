<?php

namespace app\core;

use PDO;

class Database
{

    private PDO $pdo;
    private static ?Database $database = null;

    private function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new PDO($dsn, $user, $password); // establish database connection
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    // display error messages 
    }
    public static function getInstance(array $config = null)
    {
        if (self::$database === null) {
            self::$database = new Database($config);
        }

        return self::$database;
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        // scandir() => returns all files and directories in the specified path as an array
        $files = scandir(Application::$ROOT_DIR . '/migrations');

        // array_diff() => returns all elements from $files, which are not elements of $appliedMigrations
        // in other words => $files - $appliedMigrations => returns all elements which dont exist in $appliedMigrations
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        // Loop over all migrations which are NOT already applied
        foreach ($toApplyMigrations as $migration) {
            // if the element is '.' or '..', skip the iteration
            if ($migration === '.' || $migration === '..') {
                continue;
            }
            /**
             * Requireing the migration php file, otherwise if its not required
             * the engine will look for the class inside this Database.php file
             * which will return an error since the current migration class is not
             * defined in this file
             */
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;

            // Get the filename without the file extension
            $className = pathinfo($migration, PATHINFO_FILENAME);

            // create object instance from the migration class
            $instance = new $className();
            $this->log("Applying migration: $migration");
            $instance->up();
            $this->log("Applied migration: $migration");

            // push to array which will hold migrations which were applied
            $newMigrations[] = $migration;
        }
        // if new migrations were applied insert them into DB
        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied. Nothing to migrate...");
        }
    }

    // creates migrations table if it doesn't exist in database
    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    // returns all records in migrations table (all migrations which are already applied)
    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        // turns array of strings which hold migration name surrounded by brackets into a string  
        $str = implode(",", array_map(fn ($m) => "('$m')", $migrations));

        // insert the migrations into DB
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES
            $str
        ");

        $statement->execute();
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
