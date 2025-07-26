<?php
/** @var array $featured_products */
/** @var array $most_visited_products */
/** @var array $categories */
?>

<div class="featured-products">
    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">Productos Destacados</h1>
                    <p class="lead mb-4">
                        Descubre nuestra selección de computadoras de alta calidad con las mejores opciones de financiamiento.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#featured" class="btn btn-light btn-lg">
                            <i class="fas fa-star me-2"></i>Ver Destacados
                        </a>
                        <a href="#calculator" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-calculator me-2"></i>Calcular Cuotas
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <i class="fas fa-laptop fa-10x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Destacados -->
    <section id="featured" class="mb-5">
        <div class="container">
            <h2 class="text-center mb-4">
                <i class="fas fa-star text-warning me-2"></i>Productos Destacados
            </h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card">
                            <div class="position-relative">
                                <div class="featured-badge position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star me-1"></i>Destacado
                                    </span>
                                </div>
                                <img src="/ecommerce-php/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>">
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
                                <div class="categories mb-2">
                                    <?php if (!empty($product['categories'])): ?>
                                        <?php foreach ($product['categories'] as $category): ?>
                                            <a href="/ecommerce-php/category/<?= $category['id'] ?? '#' ?>" 
                                               class="badge bg-light text-dark text-decoration-none">
                                                <?= htmlspecialchars($category['name'] ?? 'Sin categoría') ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
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
                                            <?= number_format($product['visits'] ?? 0) ?> visitas
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?= ($product['stock'] ?? 0) > 0 ? 'success' : 'danger' ?>">
                                        <?= ($product['stock'] ?? 0) > 0 ? 'En Stock' : 'Agotado' ?>
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
        </div>
    </section>

    <!-- Más Visitados por Categoría -->
    <section class="mb-5">
        <div class="container">
            <h2 class="text-center mb-4">
                <i class="fas fa-chart-line text-primary me-2"></i>Más Visitados por Categoría
            </h2>

            <div class="category-tabs mb-4">
                <ul class="nav nav-pills justify-content-center" id="categoryTabs" role="tablist">
                    <?php foreach ($categories as $index => $category): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                                    id="category-<?= $category['id'] ?>-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#category-<?= $category['id'] ?>" 
                                    type="button" 
                                    role="tab">
                                <?= htmlspecialchars($category['name'] ?? 'Sin nombre') ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tab-content" id="categoryTabsContent">
                <?php foreach ($categories as $index => $category): ?>
                    <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                         id="category-<?= $category['id'] ?>" 
                         role="tabpanel">
                        <div class="row row-cols-1 row-cols-md-4 g-4">
                            <?php foreach ($most_visited_products[$category['id']] as $product): ?>
                                <div class="col">
                                    <div class="card h-100 product-card">
                                        <img src="/ecommerce-php/assets/img/products/<?= $product['image'] ?? 'default.svg' ?>" 
                                             class="card-img-top" 
                                             alt="<?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>">
                                        <div class="card-body">
                                            <h5 class="card-title text-truncate">
                                                <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                                   class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted small">
                                                <?= htmlspecialchars($product['brand'] ?? 'Sin marca') ?> | 
                                                <?= htmlspecialchars($product['model'] ?? 'Sin modelo') ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div class="price-container">
                                                    <h4 class="mb-0 text-primary">$<?= number_format($product['price'], 2) ?></h4>
                                                    <small class="text-muted">
                                                        desde $<?= number_format($product['monthly_payment'], 2) ?>/mes
                                                    </small>
                                                </div>
                                                <div class="visits">
                                                    <small class="text-muted">
                                                        <i class="fas fa-eye me-1"></i>
                                                        <?= number_format($product['visits']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top-0 text-center">
                                            <a href="/ecommerce-php/product/<?= $product['id'] ?>" 
                                               class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fas fa-eye me-1"></i>Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Calculadora de Cuotas -->
    <section id="calculator" class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="text-center mb-4">
                                <i class="fas fa-calculator text-primary me-2"></i>
                                Calculadora de Cuotas
                            </h3>
                            <form id="installmentCalculator" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="price" class="form-label">Precio del Producto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="price" 
                                                   name="price" 
                                                   required 
                                                   min="1">
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor ingresa un precio válido
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="months" class="form-label">Plazo</label>
                                        <select class="form-select" id="months" name="months" required>
                                            <option value="">Selecciona un plazo</option>
                                            <option value="6">6 meses</option>
                                            <option value="12">12 meses</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor selecciona un plazo
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-calculator me-2"></i>Calcular
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div id="calculationResult" class="mt-4 d-none">
                                <hr>
                                <div class="row text-center">
                                    <div class="col-md-6 mb-3">
                                        <h5>Pago Mensual</h5>
                                        <h3 class="text-primary" id="monthlyPayment">$0.00</h3>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h5>Total a Pagar</h5>
                                        <h3 class="text-primary" id="totalPayment">$0.00</h3>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Los cálculos son aproximados y pueden variar. Consulta los términos y condiciones.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0) 100%);
}

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

.category-tabs .nav-pills .nav-link {
    color: var(--bs-dark);
    margin: 0 0.25rem;
}

.category-tabs .nav-pills .nav-link.active {
    background-color: var(--bs-primary);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario de calculadora
    const form = document.getElementById('installmentCalculator');
    const resultDiv = document.getElementById('calculationResult');
    const monthlyPaymentEl = document.getElementById('monthlyPayment');
    const totalPaymentEl = document.getElementById('totalPayment');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            const price = parseFloat(document.getElementById('price').value);
            const months = parseInt(document.getElementById('months').value);
            const annualInterest = 0.10; // 10% anual
            const monthlyInterest = annualInterest / 12;

            // Cálculo de pagos mensuales
            const monthlyPayment = (price * monthlyInterest * Math.pow(1 + monthlyInterest, months)) / 
                                 (Math.pow(1 + monthlyInterest, months) - 1);
            const totalPayment = monthlyPayment * months;

            monthlyPaymentEl.textContent = `$${monthlyPayment.toFixed(2)}`;
            totalPaymentEl.textContent = `$${totalPayment.toFixed(2)}`;
            resultDiv.classList.remove('d-none');
        }

        form.classList.add('was-validated');
    });

    // Manejador para los botones de cálculo de cuotas en productos
    document.querySelectorAll('.calculate-installments').forEach(button => {
        button.addEventListener('click', function() {
            const price = this.dataset.price;
            document.getElementById('price').value = price;
            document.getElementById('months').value = '6'; // valor por defecto
            document.querySelector('#calculator').scrollIntoView({ behavior: 'smooth' });
        });
    });
});
</script>