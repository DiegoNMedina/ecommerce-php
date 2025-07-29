<?php
$title = 'Productos Más Vendidos';
ob_start();
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ecommerce-php/">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Más Vendidos</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-fire text-danger me-2"></i>
                        Productos Más Vendidos
                    </h1>
                    <p class="text-muted mb-0">
                        Descubre los productos favoritos de nuestros clientes
                    </p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary fs-6">
                        <?= count($products) ?> productos
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y ordenamiento -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <label for="sortProducts" class="form-label me-2 mb-0">Ordenar por:</label>
                <select class="form-select form-select-sm" id="sortProducts" style="width: auto;">
                    <option value="sales">Más vendidos</option>
                    <option value="price_asc">Precio: menor a mayor</option>
                    <option value="price_desc">Precio: mayor a menor</option>
                    <option value="rating">Mejor valorados</option>
                    <option value="newest">Más recientes</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group" aria-label="Vista">
                <button type="button" class="btn btn-outline-secondary active" id="gridView">
                    <i class="fas fa-th"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="listView">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <!-- Sin resultados -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No hay productos vendidos aún</h3>
                    <p class="text-muted">Parece que aún no se han registrado ventas en el sistema.</p>
                    <a href="/ecommerce-php/" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Grid de productos -->
        <div class="row" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 product-card shadow-sm">
                        <div class="position-relative">
                            <!-- Badge de ventas -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-danger">
                                    <i class="fas fa-fire me-1"></i>
                                    <?= number_format($product['sales_count']) ?> vendidos
                                </span>
                            </div>
                            
                            <!-- Badge de stock -->
                            <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-warning text-dark">
                                        ¡Últimas <?= $product['stock'] ?>!
                                    </span>
                                </div>
                            <?php elseif ($product['stock'] == 0): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-secondary">
                                        Agotado
                                    </span>
                                </div>
                            <?php endif; ?>
                            
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
        <nav aria-label="Best selling products navigation">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="?page=<?= $currentPage - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" 
                           href="?page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" 
                           href="?page=<?= $currentPage + 1 ?>">
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

.badge.bg-danger {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
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

    // Cambio de vista (grid/list)
    const gridViewBtn = document.getElementById('gridView');
    const listViewBtn = document.getElementById('listView');
    const productsGrid = document.getElementById('productsGrid');

    if (gridViewBtn && listViewBtn) {
        gridViewBtn.addEventListener('click', function() {
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            productsGrid.className = 'row';
            document.querySelectorAll('.col-lg-3').forEach(col => {
                col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
            });
        });

        listViewBtn.addEventListener('click', function() {
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            productsGrid.className = 'row';
            document.querySelectorAll('.col-lg-3').forEach(col => {
                col.className = 'col-12 mb-3';
            });
        });
    }
});
</script>

<?php
$content = ob_get_clean();
echo $content;
?>