<?php

/** @var array $products */
/** @var int $currentPage */
/** @var int $totalPages */
/** @var array $categories */
?>

<div class="products-index">
    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3">Todos los Productos</h1>
                    <p class="lead mb-0">
                        Explora nuestra amplia gama de computadoras y accesorios tecnológicos.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-laptop fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filtros y Categorías -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Categorías
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="/ecommerce-php/products" class="list-group-item list-group-item-action">
                                <i class="fas fa-th-large me-2"></i>Todos los productos
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="/ecommerce-php/category/<?= $category['id'] ?>"
                                    class="list-group-item list-group-item-action">
                                    <i class="fas fa-folder me-2"></i><?= htmlspecialchars($category['name']) ?>
                                    <?php if (isset($category['product_count'])): ?>
                                        <span class="badge bg-secondary float-end"><?= $category['product_count'] ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Productos Grid -->
                <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100 product-card">
                                <div class="position-relative">
                                    <?php if (isset($product['is_featured']) && $product['is_featured']): ?>
                                        <div class="featured-badge position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star me-1"></i>Destacado
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <img src="/ecommerce-php/public_html/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>"
                                        class="card-img-top"
                                        alt="<?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>"
                                        style="height: 200px; object-fit: cover;">
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
                                            <?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small mb-2">
                                        <?= htmlspecialchars($product['brand'] ?? 'Sin marca') ?> |
                                        <?= htmlspecialchars($product['model'] ?? 'Sin modelo') ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="price-container">
                                            <h4 class="mb-0 text-primary">$<?= number_format($product['price'], 2) ?></h4>
                                            <small class="text-muted">
                                                desde $<?= number_format($product['price'] / 12, 2) ?>/mes
                                            </small>
                                        </div>
                                        <?php if (isset($product['rating'])): ?>
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
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?= ($product['stock'] ?? 0) > 0 ? 'success' : 'danger' ?>">
                                            <?= ($product['stock'] ?? 0) > 0 ? 'En Stock' : 'Agotado' ?>
                                        </span>
                                        <button class="btn btn-outline-primary btn-sm calculate-installments"
                                            data-price="<?= $product['price'] ?>">
                                            <i class="fas fa-calculator me-1"></i>Calcular
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Paginación de productos">
                        <ul class="pagination justify-content-center">
                            <!-- Página anterior -->
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/ecommerce-php/products?page=<?= $currentPage - 1 ?>">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Páginas numeradas -->
                            <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            ?>

                            <?php if ($start > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/ecommerce-php/products?page=1">1</a>
                                </li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="/ecommerce-php/products?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end < $totalPages): ?>
                                <?php if ($end < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="/ecommerce-php/products?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Página siguiente -->
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/ecommerce-php/products?page=<?= $currentPage + 1 ?>">
                                        Siguiente <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .product-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .stars {
        font-size: 0.8rem;
    }

    .featured-badge {
        z-index: 10;
    }
</style>