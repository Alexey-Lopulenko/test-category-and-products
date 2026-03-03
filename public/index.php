<?php
declare(strict_types=1);

// Initial state from URL (restore after reload)
$initialCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$initialSort = isset($_GET['sort']) ? (string)$_GET['sort'] : 'newest';

// Pass PHP -> JS as JSON (required)
$initialState = [
    'category_id' => $initialCategoryId > 0 ? $initialCategoryId : null,
    'sort' => in_array($initialSort, ['cheap','alpha','newest'], true) ? $initialSort : 'newest',
];
?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Каталог</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="row g-3">
        <aside class="col-12 col-md-3">
            <div class="card">
                <div class="card-header fw-semibold">Категорії</div>
                <div class="list-group list-group-flush" id="js-categories">
                    <div class="p-3 text-muted">Завантаження...</div>
                </div>
            </div>
        </aside>

        <main class="col-12 col-md-9">
            <div class="d-flex gap-2 align-items-center mb-3">
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <label class="form-label m-0">Сортування:</label>
                    <select class="form-select" id="js-sort" style="width:220px;">
                        <option value="cheap">Спочатку дешевші</option>
                        <option value="alpha">По алфавіту</option>
                        <option value="newest">Спочатку нові</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold d-flex justify-content-between">
                    <span>Товари</span>
                    <span class="text-muted small" id="js-meta"></span>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="js-products">
                        <div class="text-muted">Завантаження...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Товар</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрити"></button>
            </div>
            <div class="modal-body" id="js-modal-body">
                Завантаження...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                <button type="button" class="btn btn-primary">Оформити</button>
            </div>
        </div>
    </div>
</div>

<script>
    // PHP -> JS as JSON (required)
    window.__INITIAL_STATE__ = <?php echo json_encode($initialState, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/app.js"></script>
</body>
</html>