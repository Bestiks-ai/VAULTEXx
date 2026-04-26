<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Transaction
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function create(int $userId, int $walletId, string $type, float $amount, string $status = 'pending'): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (user_id, wallet_id, type, amount, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$userId, $walletId, $type, $amount, $status]);
        return (int) $this->db->lastInsertId();
    }

    public function findByUserId(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, w.currency FROM transactions t 
             JOIN wallets w ON t.wallet_id = w.id 
             WHERE t.user_id = ? 
             ORDER BY t.created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE transactions SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public function getTotalCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM transactions");
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }
}
