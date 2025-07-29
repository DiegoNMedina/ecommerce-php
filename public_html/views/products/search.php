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
        <!-- Contenedor principal con sidebar -->
        <div class="row">
            <!-- Sidebar de filtros -->
            <div class="col-lg-3 col-md-4">
                <!-- Botón para mostrar filtros en móvil -->
                <div class="d-md-none mb-3">
                    <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#mobileFilters" aria-expanded="false" aria-controls="mobileFilters">
                        <i class="fas fa-filter me-2"></i>Mostrar Filtros
                    </button>
                </div>
                
                <!-- Filtros colapsables en móvil, normales en desktop -->
                <div class="collapse d-md-block" id="mobileFilters">
                    <div class="card mb-4 sticky-top" style="top: 20px;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
                            <button type="button" class="btn-close d-md-none" data-bs-toggle="collapse" data-bs-target="#mobileFilters" aria-label="Cerrar filtros"></button>
                        </div>
                        <div class="card-body">
                        <form action="/ecommerce-php/search" method="GET" id="filterForm">
                            <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">
                            
                            <!-- Filtro por Categoría -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Categoría</label>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="categories[]" value="<?= $category['id'] ?>"
                                                   id="cat_<?= $category['id'] ?>"
                                                   <?= in_array($category['id'], $selectedCategories ?? []) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="cat_<?= $category['id'] ?>">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Filtro por Marca -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Marca</label>
                                <?php if (!empty($brands)): ?>
                                    <?php foreach ($brands as $brand): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="brands[]" value="<?= htmlspecialchars($brand) ?>"
                                                   id="brand_<?= md5($brand) ?>"
                                                   <?= in_array($brand, $selectedBrands ?? []) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="brand_<?= md5($brand) ?>">
                                                <?= htmlspecialchars($brand) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Filtro por Rango de Precio -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rango de Precio</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="min_price" placeholder="Mín"
                                               value="<?= htmlspecialchars($minPrice ?? '') ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="max_price" placeholder="Máx"
                                               value="<?= htmlspecialchars($maxPrice ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Aplicar Filtros
                                </button>
                                <a href="/ecommerce-php/search?q=<?= urlencode($query) ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Limpiar Filtros
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- Contenido principal -->
            <div class="col-lg-9 col-md-8">
                <!-- Barra de ordenamiento -->
                <div class="card mb-4">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <?= $totalProducts ?> producto(s) encontrado(s) para "<?= htmlspecialchars($query) ?>"
                            </span>
                            <div class="d-flex align-items-center">
                                <label for="sortProducts" class="form-label me-2 mb-0">Ordenar por:</label>
                                <select class="form-select form-select-sm" id="sortProducts" style="width: auto;">
                                    <option value="relevance" <?= ($sort ?? 'relevance') === 'relevance' ? 'selected' : '' ?>>
                                        Relevancia
                                    </option>
                                    <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>
                                        Precio: Menor a Mayor
                                    </option>
                                    <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>
                                        Precio: Mayor a Menor
                                    </option>
                                    <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>
                                        Nombre: A-Z
                                    </option>
                                    <option value="name_desc" <?= ($sort ?? '') === 'name_desc' ? 'selected' : '' ?>>
                                        Nombre: Z-A
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- Lista de Productos -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
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
            </div>
        </div>
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

    /* Estilos para móvil */
    @media (max-width: 767.98px) {
        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
        
        #mobileFilters .card {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        #mobileFilters .card-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .btn-close {
            font-size: 0.75rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Manejador para el ordenamiento
        const sortSelect = document.getElementById('sortProducts');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const filterForm = document.getElementById('filterForm');
                if (filterForm) {
                    // Crear un input hidden para el sort
                    let sortInput = filterForm.querySelector('input[name="sort"]');
                    if (!sortInput) {
                        sortInput = document.createElement('input');
                        sortInput.type = 'hidden';
                        sortInput.name = 'sort';
                        filterForm.appendChild(sortInput);
                    }
                    sortInput.value = this.value;
                    filterForm.submit();
                } else {
                    // Fallback si no hay formulario de filtros
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort', this.value);
                    window.location.href = currentUrl.toString();
                }
            });
        }

        // Auto-submit de filtros cuando cambian
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Opcional: auto-submit cuando cambian los filtros
                    // filterForm.submit();
                });
            });
            
            // Cerrar filtros en móvil después de aplicar
            filterForm.addEventListener('submit', function() {
                if (window.innerWidth < 768) {
                    const mobileFilters = document.getElementById('mobileFilters');
                    if (mobileFilters && mobileFilters.classList.contains('show')) {
                        setTimeout(() => {
                            const bsCollapse = new bootstrap.Collapse(mobileFilters, {
                                toggle: false
                            });
                            bsCollapse.hide();
                        }, 100);
                    }
                }
            });
        }
        
        // Mejorar experiencia táctil en móvil
        if (window.innerWidth < 768) {
            const filterButton = document.querySelector('[data-bs-target="#mobileFilters"]');
            if (filterButton) {
                filterButton.addEventListener('click', function() {
                    // Scroll suave hacia los filtros cuando se abren
                    setTimeout(() => {
                        this.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 350);
                });
            }
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