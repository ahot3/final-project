<?php
namespace app\models;

use PDO;

class Review extends Model
{
    public int $id;
    public string $destination;
    public string $reviewer_name;
    public string $comment;
    public int $stars;
    public string $created_at;

    public function __construct(array $r)
    {
        $this->id = (int)$r['id'];
        $this->destination = $r['destination'];
        $this->reviewer_name = $r['reviewer_name'];
        $this->comment = $r['comment'];
        $this->stars = (int)$r['stars'];
        $this->created_at = $r['created_at'];
    }

    public static function forDestination(string $dest): array
    {
        // Sanitize input
        $dest = htmlspecialchars($dest, ENT_QUOTES, 'UTF-8');
        
        $stmt = self::db()->prepare(
            "SELECT * FROM reviews 
             WHERE destination = :d 
             ORDER BY created_at DESC"
        );
        $stmt->execute(['d' => $dest]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function add(
        string $dest,
        string $name,
        string $comment,
        int $stars
    ): self {
        // Sanitize inputs
        $dest = htmlspecialchars($dest, ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
        $stars = min(5, max(1, (int)$stars)); // Ensure stars is between 1-5
        
        $stmt = self::db()->prepare(
            "INSERT INTO reviews 
               (destination, reviewer_name, comment, stars)
             VALUES 
               (:d, :n, :c, :s)"
        );
        $stmt->execute([
            'd' => $dest,
            'n' => $name,
            'c' => $comment,
            's' => $stars
        ]);
        $id = (int) self::db()->lastInsertId();
        return new self([
            'id' => $id,
            'destination' => $dest,
            'reviewer_name' => $name,
            'comment' => $comment,
            'stars' => $stars,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}