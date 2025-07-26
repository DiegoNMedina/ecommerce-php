<?php
/** @var array $products */
/** @var string $query */
/** @var int $total_pages */
/** @var int $current_page */
?>

<div class="search-results">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="fas fa-search me-2"></i>
                Resultados de búsqueda para: "<?= htmlspecialchars($query) ?>"
            </h2>
            <p class="text-muted">
                Se encontraron <?= count($products) ?> productos
            </p>
        </div>
        <div class="col-md-4">
            <form action="/ecommerce-php/search" method="GET" class="search-form">
                <div class="input-group">
                    <input type="text" 
                           name="q" 
                           class="form-control" 
                           placeholder="Buscar productos..." 
                           value="<?= htmlspecialchars($query) ?>"
                           required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No se encontraron productos que coincidan con tu búsqueda.
            <hr>
            Sugerencias:
            <ul class="mb-0">
                <li>Verifica que las palabras estén escritas correctamente</li>
                <li>Utiliza palabras más generales o menos palabras</li>
                <li>Prueba con términos similares o sinónimos</li>
            </ul>
        </div>

        <!-- Productos Sugeridos -->
        <?php if (!empty($suggested_products)): ?>
            <div class="mt-5">
                <h3>Productos que te podrían interesar</h3>
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    <?php foreach ($suggested_products as $product): ?>
                        <div class="col">
                            <div class="card h-100 product-card">
                                <img src="/ecommerce-php/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">
                                        <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">
                                        <?= htmlspecialchars($product['brand']) ?> | 
                                        <?= htmlspecialchars($product['model']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div class="price-container">
                                            <h4 class="mb-0 text-primary">$<?= number_format($product['price'], 2) ?></h4>
                                            <small class="text-muted">
                                                desde $<?= number_format($product['monthly_payment'], 2) ?>/mes
                                            </small>
                                        </div>
                                        <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="filters">
                                <button class="btn btn-outline-primary me-2 dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 300px;">
                                    <form action="/ecommerce-php/search" method="GET" id="filterForm">
                                        <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Rango de Precio</label>
                                            <div class="row g-2">
                                                <div class="col">
                                                    <input type="number" 
                                                           name="min_price" 
                                                           class="form-control" 
                                                           placeholder="Mín">
                                                </div>
                                                <div class="col">
                                                    <input type="number" 
                                                           name="max_price" 
                                                           class="form-control" 
                                                           placeholder="Máx">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Marca</label>
                                            <select name="brand" class="form-select">
                                                <option value="">Todas las marcas</option>
                                                <?php foreach ($brands as $brand): ?>
                                                    <option value="<?= htmlspecialchars($brand) ?>">
                                                        <?= htmlspecialchars($brand) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Categoría</label>
                                            <select name="category" class="form-select">
                                                <option value="">Todas las categorías</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>">
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="checkbox" 
                                                   name="in_stock" 
                                                   class="form-check-input" 
                                                   id="inStock">
                                            <label class="form-check-label" for="inStock">
                                                Solo productos en stock
                                            </label>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            Aplicar Filtros
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="sorting">
                                <select class="form-select" id="sortProducts">
                                    <option value="relevance">Relevancia</option>
                                    <option value="price_asc">Precio: Menor a Mayor</option>
                                    <option value="price_desc">Precio: Mayor a Menor</option>
                                    <option value="name_asc">Nombre: A-Z</option>
                                    <option value="name_desc">Nombre: Z-A</option>
                                    <option value="visits_desc">Más Visitados</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Productos -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            <img src="/ecommerce-php/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($product['model']) ?>">
                            <div class="product-overlay">
                                <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-truncate">
                                <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['model']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small mb-2">
                                <?= htmlspecialchars($product['brand']) ?> | 
                                <?= htmlspecialchars($product['model']) ?>
                            </p>
                            <div class="categories mb-2">
                                <?php foreach ($product['categories'] as $category): ?>
                                    <a href="/ecommerce-php/category/<?= $category['id'] ?>" 
                                       class="badge bg-light text-dark text-decoration-none">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-container">
                                    <h4 class="mb-0 text-primary">$<?= number_format($product['price'], 2) ?></h4>
                                    <small class="text-muted">
                                        desde $<?= number_format($product['price'] / 12, 2) ?>/mes
                                    </small>
                                </div>
                                <div class="rating-container text-end">
                                    <div class="stars">
                                        <?php 
                                        $rating = isset($product['avg_rating']) ? round($product['avg_rating']) : 0;
                                        for ($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <?php if ($i <= $rating): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= number_format($product['visits']) ?> visitas
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                    <?= $product['stock'] > 0 ? 'En Stock' : 'Agotado' ?>
                                </span>
                                <button class="btn btn-outline-primary btn-sm calculate-installments" 
                                        data-price="<?= $product['price'] ?>">
                                    <i class="fas fa-calculator me-1"></i>Calcular Cuotas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Search results navigation">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="?q=<?= urlencode($query) ?>&page=<?= $currentPage - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" 
                           href="?q=<?= urlencode($query) ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="?q=<?= urlencode($query) ?>&page=<?= $currentPage + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.card-img-top {
    height: 200px;
    object-fit: contain;
    padding: 1rem;
}

.stars {
    font-size: 0.8rem;
}

.categories .badge {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.categories .badge:hover {
    background-color: var(--bs-primary) !important;
    color: white !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejador para el ordenamiento
    const sortSelect = document.getElementById('sortProducts');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', this.value);
            window.location.href = currentUrl.toString();
        });
    }

    // Manejador para el cálculo de cuotas
    document.querySelectorAll('.calculate-installments').forEach(button => {
        button.addEventListener('click', function() {
            const price = this.dataset.price;
            window.location.href = `/ecommerce-php/calculator?price=${price}`;
        });
    });

    // Búsqueda en tiempo real con debounce
    const searchInput = document.querySelector('.search-form input[name="q"]');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    fetch(`/ecommerce-php/api/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            // Aquí podrías mostrar sugerencias de búsqueda
                            console.log(data);
                        })
                        .catch(error => console.error('Error:', error));
                }, 300);
            }
        });
    }
});
</script>