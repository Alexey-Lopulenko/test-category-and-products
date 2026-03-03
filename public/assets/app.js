(() => {
    const categoriesEl = document.getElementById('js-categories');
    const productsEl = document.getElementById('js-products');
    const sortEl = document.getElementById('js-sort');
    const metaEl = document.getElementById('js-meta');

    const modalEl = document.getElementById('productModal');
    const modalBodyEl = document.getElementById('js-modal-body');
    const bsModal = new bootstrap.Modal(modalEl);

    const state = {
        category_id: window.__INITIAL_STATE__?.category_id ?? null,
        sort: window.__INITIAL_STATE__?.sort ?? 'newest',
        categories: [],
    };

    function buildUrlParams() {
        const params = new URLSearchParams();
        if (state.category_id) params.set('category_id', String(state.category_id));
        if (state.sort) params.set('sort', state.sort);
        return params;
    }

    function syncUrl(push = true) {
        const params = buildUrlParams();
        const url = params.toString() ? `?${params.toString()}` : location.pathname;
        if (push) history.pushState({ ...state }, '', url);
        else history.replaceState({ ...state }, '', url);
    }

    async function fetchJson(url) {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return await res.json();
    }

    function renderCategories() {
        const activeId = state.category_id;

        const allBtn = `
      <button class="list-group-item list-group-item-action ${activeId ? '' : 'active'}"
              data-category-id="">
        Всі <span class="badge bg-secondary float-end">${state.categories.reduce((s,c)=>s+Number(c.product_count),0)}</span>
      </button>
    `;

        const items = state.categories.map(c => {
            const isActive = activeId === Number(c.id);
            return `
        <button class="list-group-item list-group-item-action ${isActive ? 'active' : ''}"
                data-category-id="${c.id}">
          ${escapeHtml(c.name)}
          <span class="badge bg-secondary float-end">${c.product_count}</span>
        </button>
      `;
        }).join('');

        categoriesEl.innerHTML = allBtn + items;
    }

    function renderProducts(products) {
        if (!products.length) {
            productsEl.innerHTML = `<div class="text-muted">Немає товарів.</div>`;
            metaEl.textContent = '';
            return;
        }

        metaEl.textContent = `Показано: ${products.length}`;

        productsEl.innerHTML = products.map(p => `
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="fw-semibold mb-1">${escapeHtml(p.name)}</div>
            <div class="text-muted small mb-2">Дата: ${escapeHtml(p.created_at)}</div>
            <div class="fs-5 mb-3">${Number(p.price).toFixed(2)} грн</div>
            <button class="btn btn-primary w-100 js-buy" data-product-id="${p.id}">
              Купити
            </button>
          </div>
        </div>
      </div>
    `).join('');
    }

    async function loadCategories() {
        const data = await fetchJson('/api/categories.php');
        state.categories = data.categories || [];

        // If category_id in URL doesn't exist -> reset
        if (state.category_id) {
            const exists = state.categories.some(c => Number(c.id) === Number(state.category_id));
            if (!exists) state.category_id = null;
        }

        renderCategories();
    }

    async function loadProducts() {
        productsEl.innerHTML = `<div class="text-muted">Завантаження...</div>`;
        const params = new URLSearchParams();
        if (state.category_id) params.set('category_id', String(state.category_id));
        params.set('sort', state.sort);

        const data = await fetchJson(`/api/products.php?${params.toString()}`);
        renderProducts(data.products || []);
    }

    async function openProductModal(productId) {
        modalBodyEl.textContent = 'Завантаження...';
        bsModal.show();

        const data = await fetchJson(`/api/product.php?id=${encodeURIComponent(productId)}`);
        const p = data.product;

        modalBodyEl.innerHTML = `
      <div class="mb-2"><span class="text-muted">Категорія:</span> ${escapeHtml(p.category_name)}</div>
      <div class="h5 mb-2">${escapeHtml(p.name)}</div>
      <div class="mb-2"><span class="text-muted">Дата:</span> ${escapeHtml(p.created_at)}</div>
      <div class="fs-4 fw-semibold">${Number(p.price).toFixed(2)} грн</div>
    `;
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // Events
    categoriesEl.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-category-id]');
        if (!btn) return;

        const raw = btn.getAttribute('data-category-id');
        state.category_id = raw ? Number(raw) : null;

        renderCategories();
        syncUrl(true);        // add GET params without reload
        await loadProducts(); // ajax reload list
    });

    sortEl.addEventListener('change', async () => {
        state.sort = sortEl.value;
        syncUrl(true);        // add GET params without reload
        await loadProducts(); // ajax resort
    });

    productsEl.addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-buy');
        if (!btn) return;
        const id = Number(btn.dataset.productId);
        if (!id) return;
        await openProductModal(id);
    });

    // Back/Forward restore
    window.addEventListener('popstate', async (e) => {
        const s = e.state;
        if (!s) return;

        state.category_id = s.category_id ?? null;
        state.sort = s.sort ?? 'newest';
        sortEl.value = state.sort;

        renderCategories();
        await loadProducts();
    });

    // Init
    (async () => {
        sortEl.value = state.sort;
        syncUrl(false); // normalize URL (replaceState)

        await loadCategories();
        await loadProducts();
    })();
})();