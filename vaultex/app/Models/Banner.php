<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Banner
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function getAllActive(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order ASC"
        );
        return $stmt->fetchAll();
    }

    public function create(string $title, string $subtitle, string $image, string $link = '#', string $altText = ''): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO banners (title, subtitle, image, link, alt_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$title, $subtitle, $image, $link, $altText]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $allowed = ['title', 'subtitle', 'image', 'link', 'alt_text', 'is_active', 'sort_order'];
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            return;
        }

        $values[] = $id;
        $sql = "UPDATE banners SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function toggleActive(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE banners SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
    }
}
