<?php

declare(strict_types=1);

require __DIR__ . '/../../app/autoload.php';
$config = require __DIR__ . '/../../.env.php';

use App\Core\DB;
use App\Core\Response;
use App\Repositories\ProductRepository;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    Response::json(['ok' => false, 'error' => 'Invalid id'], 400);
}

$pdo = DB::pdo($config['db']);
$repo = new ProductRepository($pdo);

$product = $repo->find($id);
if (!$product) {
    Response::json(['ok' => false, 'error' => 'Not found'], 404);
}

Response::json(['ok' => true, 'product' => $product]);