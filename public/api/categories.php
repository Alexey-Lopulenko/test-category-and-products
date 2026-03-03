<?php

declare(strict_types=1);

require __DIR__ . '/../../app/autoload.php';
$config = require __DIR__ . '/../../.env.php';

use App\Core\DB;
use App\Core\Response;
use App\Repositories\CategoryRepository;

$pdo = DB::pdo($config['db']);
$repo = new CategoryRepository($pdo);

Response::json([
    'ok' => true,
    'categories' => $repo->allWithCounts(),
]);