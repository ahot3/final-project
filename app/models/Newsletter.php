<?php
namespace app\models;

use PDO;

class Newsletter extends Model
{
    public int $id;
    public string $email;
    public string $name;
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->email = $data['email'];
        $this->name = $data['name'];
        $this->created_at = $data['created_at'];
    }

    public static function subscribe(string $email, string $name = ''): self
    {
        error_log("Newsletter::subscribe - Starting for email: " . $email);
        
        // Sanitize inputs
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        error_log("Newsletter::subscribe - Sanitized: email=" . $email . ", name=" . $name);
        
        try {
            error_log("Newsletter::subscribe - Getting database connection");
            $db = self::db();
            error_log("Newsletter::subscribe - Database connection successful");
            
            // Check if the newsletter_subscribers table exists
            $tableCheck = $db->query("SHOW TABLES LIKE 'newsletter_subscribers'");
            if ($tableCheck->rowCount() === 0) {
                error_log("Newsletter::subscribe - Table 'newsletter_subscribers' doesn't exist");
                
                // Create the table if it doesn't exist
                $createTable = "CREATE TABLE `newsletter_subscribers` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `email` varchar(100) NOT NULL,
                    `name` varchar(100) DEFAULT '',
                    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                    `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
                
                $db->exec($createTable);
                error_log("Newsletter::subscribe - Created 'newsletter_subscribers' table");
            }
            
            $sql = "INSERT INTO newsletter_subscribers (email, name, created_at)
                   VALUES (:email, :name, NOW())
                   ON DUPLICATE KEY UPDATE 
                   name = IF(:name != '', :name, name),
                   updated_at = NOW()";
            
            error_log("Newsletter::subscribe - SQL: " . $sql);
            
            $stmt = $db->prepare($sql);
            
            $params = [
                'email' => $email,
                'name' => $name
            ];
            error_log("Newsletter::subscribe - Params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            error_log("Newsletter::subscribe - Execute result: " . ($result ? 'true' : 'false'));
            
            if (!$result) {
                error_log("Newsletter::subscribe - SQL error info: " . print_r($stmt->errorInfo(), true));
                throw new \Exception("SQL execution failed: " . implode(' - ', $stmt->errorInfo()));
            }
            
            if ($stmt->rowCount() === 0) {
                error_log("Newsletter::subscribe - No rows affected, getting existing record");
                
                $stmt = $db->prepare("SELECT * FROM newsletter_subscribers WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $data = $stmt->fetch();
                
                if (!$data) {
                    error_log("Newsletter::subscribe - Could not find record for email: " . $email);
                    throw new \Exception("Failed to find subscriber record");
                }
                
                error_log("Newsletter::subscribe - Found existing subscriber: " . print_r($data, true));
                return new self($data);
            }
            
            $id = (int)$db->lastInsertId();
            error_log("Newsletter::subscribe - New ID: " . $id);
            
            $now = date('Y-m-d H:i:s');
            $result = [
                'id' => $id,
                'email' => $email,
                'name' => $name,
                'created_at' => $now
            ];
            
            error_log("Newsletter::subscribe - Returning new subscriber: " . print_r($result, true));
            return new self($result);
            
        } catch (\PDOException $e) {
            error_log("PDOException in Newsletter::subscribe: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new \Exception("Database error occurred: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Exception in Newsletter::subscribe: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function getAll(): array
    {
        error_log("Newsletter::getAll - Starting");
        
        try {
            $db = self::db();
            $stmt = $db->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Newsletter::getAll - Found " . count($rows) . " subscribers");
            return array_map(fn($row) => new self($row), $rows);
        } catch (\Exception $e) {
            error_log("Exception in Newsletter::getAll: " . $e->getMessage());
            throw $e;
        }
    }
}