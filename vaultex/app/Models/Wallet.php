<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Wallet
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $wallet = $stmt->fetch();
        return $wallet ?: null;
    }

    public function create(int $userId, string $currency, string $address): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO wallets (user_id, currency, address, balance, created_at) VALUES (?, ?, ?, 0, NOW())"
        );
        $stmt->execute([$userId, $currency, $address]);
        return (int) $this->db->lastInsertId();
    }

    public function updateBalance(int $walletId, float $amount): void
    {
        $stmt = $this->db->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $walletId]);
    }

    public function getBalance(int $walletId): float
    {
        $stmt = $this->db->prepare("SELECT balance FROM wallets WHERE id = ?");
        $stmt->execute([$walletId]);
        $result = $stmt->fetch();
        return (float) ($result['balance'] ?? 0);
    }
}
