<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Returns categories with product_count.
     */
    public function allWithCounts(): array
    {
        $sql = "
            SELECT c.id, c.name, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }
}