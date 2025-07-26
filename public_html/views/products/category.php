<?php

/** @var array $products */
/** @var array $category */
/** @var array $subcategories */
/** @var array $accessories */
/** @var array $breadcrumbs */
/** @var int $total_pages */
/** @var int $current_page */
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/ecommerce-php/">Inicio</a></li>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if ($index === array_key_last($breadcrumbs)): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($crumb['name']) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item">
                    <a href="/ecommerce-php/category/<?= $crumb['id'] ?>"><?= htmlspecialchars($crumb['name']) ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-4">
        <!-- Subcategorías -->
        <?php if (!empty($subcategories)): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Subcategorías
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($subcategories as $subcat): ?>
                        <a href="/ecommerce-php/category/<?= $subcat['id'] ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($subcat['name']) ?>
                            <span class="badge bg-primary rounded-pill"><?= $subcat['product_count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Accesorios Relacionados -->
        <?php if (!empty($accessories)): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plug me-2"></i>Accesorios Relacionados
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($accessories as $accessory): ?>
                        <a href="/ecommerce-php/product/<?= $accessory['id'] ?>"
                            class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= htmlspecialchars($accessory['name']) ?></h6>
                                <small class="text-primary">$<?= number_format($accessory['price'], 2) ?></small>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($accessory['brand']) ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Productos -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?= htmlspecialchars($category['name']) ?></h2>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-2"></i>Ordenar por
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="?sort=price_asc">Precio: Menor a Mayor</a></li>
                    <li><a class="dropdown-item" href="?sort=price_desc">Precio: Mayor a Menor</a></li>
                    <li><a class="dropdown-item" href="?sort=name_asc">Nombre: A-Z</a></li>
                    <li><a class="dropdown-item" href="?sort=name_desc">Nombre: Z-A</a></li>
                    <li><a class="dropdown-item" href="?sort=visits_desc">Más Visitados</a></li>
                </ul>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No se encontraron productos en esta categoría.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card">
                            <div class="position-relative">
                                <img src="/ecommerce-php/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>"
                                    class="card-img-top"
                                    alt="<?= htmlspecialchars($product['name']) ?>">
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
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-2">
                                    <?= htmlspecialchars($product['brand']) ?> |
                                    <?= htmlspecialchars($product['model']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-container">
                                        <h4 class="mb-0 text-primary">$<?= number_format($product['price'], 2) ?></h4>
                                        <small class="text-muted">
                                            desde $<?= number_format($product['monthly_payment'], 2) ?>/mes
                                        </small>
                                    </div>
                                    <div class="rating-container text-end">
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $product['rating']): ?>
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
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Product navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $current_page + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejador para el cálculo de cuotas
        document.querySelectorAll('.calculate-installments').forEach(button => {
            button.addEventListener('click', function() {
                const price = this.dataset.price;
                window.location.href = `/ecommerce-php/calculator?price=${price}`;
            });
        });
    });
</script>