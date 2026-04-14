<?php
namespace App\Core;

use PDO;
use PDOException;

class MigrationRunner
{
    private PDO $db;
    private string $migrationsPath;
    private static bool $hasRun = false;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsPath = dirname(__DIR__, 2) . '/migrations';
    }

    /**
     * Run all pending migrations from the migrations/ directory.
     * Executes at most once per process.
     */
    public function run(): void
    {
        if (self::$hasRun) {
            return;
        }
        self::$hasRun = true;

        $this->ensureMigrationsTable();

        $applied = $this->getAppliedMigrations();
        $files   = $this->getMigrationFiles();

        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false || trim($sql) === '') {
                continue;
            }

            try {
                $this->db->exec($sql);
            } catch (PDOException $e) {
                // Tolerate "already exists" / "duplicate column" errors so
                // migrations that were partially applied before the tracker
                // existed don't block subsequent runs.
                $code = (int) $e->errorInfo[1];
                $toleratedCodes = [
                    1060, // Duplicate column name
                    1061, // Duplicate key name
                    1050, // Table already exists
                    1054, // Unknown column (e.g. UPDATE on a column that was already renamed)
                ];
                if (!in_array($code, $toleratedCodes, true)) {
                    // Re-throw unexpected errors so they surface normally.
                    throw $e;
                }
            }

            $this->recordMigration($name);
        }
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }

    /**
     * @return string[] List of migration filenames already applied.
     */
    private function getAppliedMigrations(): array
    {
        $stmt = $this->db->query('SELECT migration FROM migrations ORDER BY id');
        if ($stmt === false) {
            return [];
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return string[] Sorted list of absolute paths to .sql migration files.
     */
    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '/*.sql');
        if ($files === false) {
            return [];
        }
        sort($files);
        return $files;
    }

    private function recordMigration(string $name): void
    {
        $stmt = $this->db->prepare('INSERT INTO migrations (migration) VALUES (?)');
        $stmt->execute([$name]);
    }
}
