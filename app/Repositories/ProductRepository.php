<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductRepository
{
    public const SORT_CHEAP  = 'cheap';
    public const SORT_ALPHA  = 'alpha';
    public const SORT_NEWEST = 'newest';

    public function __construct(private PDO $pdo) {}

    public static function normalizeSort(?string $sort): string
    {
        return match ($sort) {
            self::SORT_CHEAP, self::SORT_ALPHA, self::SORT_NEWEST => $sort,
            default => self::SORT_NEWEST,
        };
    }

    private function orderBy(string $sort): string
    {
        return match ($sort) {
            self::SORT_CHEAP  => 'p.price ASC, p.id DESC',
            self::SORT_ALPHA  => 'p.name ASC, p.id DESC',
            default           => 'p.created_at DESC, p.id DESC',
        };
    }

    public function list(?int $categoryId, string $sort, int $limit = 50): array
    {
        $sort = self::normalizeSort($sort);

        $where = '';
        $params = [];
        if ($categoryId !== null && $categoryId > 0) {
            $where = 'WHERE p.category_id = :cid';
            $params[':cid'] = $categoryId;
        }

        $sql = "
            SELECT p.id, p.category_id, p.name, p.price, p.created_at
            FROM products p
            $where
            ORDER BY {$this->orderBy($sort)}
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "
            SELECT p.id, p.category_id, p.name, p.price, p.created_at, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}