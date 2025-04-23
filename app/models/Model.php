<?php
namespace app\models;

use PDO;

class Model
{
    private static ?PDO $db = null;

    protected static function db(): PDO
    {
        if (self::$db === null) {
            error_log("Model::db - Creating new database connection");
            error_log("Model::db - DB_HOST: " . DB_HOST);
            error_log("Model::db - DB_NAME: " . DB_NAME);
            error_log("Model::db - DB_USER: " . DB_USER);
            // Not logging password for security reasons
            
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                error_log("Model::db - DSN: " . $dsn);
                
                self::$db = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                
                error_log("Model::db - PDO connection successful");
                
                // Test the connection with a simple query
                $test = self::$db->query("SELECT 1");
                $result = $test->fetch();
                error_log("Model::db - Test query result: " . print_r($result, true));
                
                // Get MySQL version
                $version = self::$db->query("SELECT VERSION() as version")->fetch();
                error_log("Model::db - MySQL version: " . ($version['version'] ?? 'unknown'));
                
                // Check if reviews table exists
                $tableCheck = self::$db->query("SHOW TABLES LIKE 'reviews'")->rowCount();
                error_log("Model::db - 'reviews' table exists: " . ($tableCheck > 0 ? 'Yes' : 'No'));
                
                if ($tableCheck > 0) {
                    // Get table structure
                    $columns = self::$db->query("DESCRIBE reviews")->fetchAll(PDO::FETCH_ASSOC);
                    error_log("Model::db - 'reviews' table structure: " . print_r($columns, true));
                }
                
            } catch (\PDOException $e) {
                error_log("Model::db - PDO connection error: " . $e->getMessage());
                error_log("Model::db - Error code: " . $e->getCode());
                error_log("Model::db - Stack trace: " . $e->getTraceAsString());
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$db;
    }
}