<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password_hash, created_at) VALUES (?, ?, NOW())"
        );
        $stmt->execute([$email, $passwordHash]);
        return (int) $this->db->lastInsertId();
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
