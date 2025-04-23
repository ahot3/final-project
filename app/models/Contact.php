<?php
namespace app\models;

class Contact extends Model
{
    public int $id;
    public string $name;
    public string $email;
    public string $message;
    public string $subject;
    public string $created_at;

    public function __construct(array $row)
    {
        $this->id = (int)$row['id'];
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->message = $row['message'];
        $this->subject = $row['subject'] ?? '';
        $this->created_at = $row['created_at'];
    }

    public static function all(): array
    {
        $stmt = self::db()->query("SELECT * FROM contacts ORDER BY created_at DESC");
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function add(string $name, string $email, string $message, string $subject = ''): self
    {
        // Sanitize inputs
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        
        $stmt = self::db()->prepare(
            "INSERT INTO contacts (name, email, message, subject, created_at)
             VALUES (:n, :e, :m, :s, NOW())"
        );
        $stmt->execute([
            'n' => $name,
            'e' => $email,
            'm' => $message,
            's' => $subject
        ]);
        
        $id = (int)self::db()->lastInsertId();
        
        return new self([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'subject' => $subject,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}