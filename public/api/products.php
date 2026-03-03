<?php

declare(strict_types=1);

require __DIR__ . '/../../app/autoload.php';
$config = require __DIR__ . '/../../.env.php';

use App\Core\DB;
use App\Core\Response;
use App\Repositories\ProductRepository;

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$sort = isset($_GET['sort']) ? (string)$_GET['sort'] : ProductRepository::SORT_NEWEST;

$pdo = DB::pdo($config['db']);
$repo = new ProductRepository($pdo);

Response::json([
    'ok' => true,
    'category_id' => $categoryId,
    'sort' => ProductRepository::normalizeSort($sort),
    'products' => $repo->list($categoryId, $sort),
]);