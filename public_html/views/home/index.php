<div class="hero-section bg-light py-5 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Encuentra tu computadora ideal</h1>
                <p class="lead">Las mejores marcas con facilidades de pago hasta 12 meses sin intereses</p>
                <a href="/ecommerce-php/products" class="btn btn-primary btn-lg">Ver Catálogo</a>
            </div>
            <div class="col-md-6">
                <img src="/ecommerce-php/assets/img/hero-image.svg" alt="Computadoras" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="categories-section mb-5">
    <h2 class="text-center mb-4">Categorías Principales</h2>
    <div class="row">
        <?php foreach ($categories as $category): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                        <ul class="list-unstyled">
                            <?php foreach ($category['children'] as $subcategory): ?>
                                <li>
                                    <a href="/ecommerce-php/category/<?= $subcategory['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($subcategory['name']) ?>
                                        <span class="badge bg-secondary"><?= $subcategory['product_count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="featured-products mb-5">
    <h2 class="text-center mb-4">Productos Destacados</h2>
    <div class="row">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="/ecommerce-php/product/<?= $product['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php 
                            $specs = json_decode($product['specifications'], true);
                            echo htmlspecialchars($specs['CPU'] . ' / ' . $specs['RAM']);
                            ?>
                        </p>
                        <div class="price-section">
                            <h4 class="text-primary mb-2">$<?= number_format($product['price'], 2) ?></h4>
                            <small class="text-muted d-block">
                                Desde $<?= number_format($product['installments'][12], 2) ?> mensuales
                            </small>
                        </div>
                        <div class="mt-3">
                            <span class="text-muted">
                                <i class="fas fa-eye"></i> <?= $product['visits'] ?>
                            </span>
                            <span class="text-muted ms-2">
                                <i class="fas fa-star"></i> <?= number_format($product['avg_rating'] ?? 0, 1) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/ecommerce-php/product/<?= $product['id'] ?>" class="btn btn-outline-primary w-100">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="category-products">
    <?php foreach ($mostVisitedByCategory as $categoryName => $products): ?>
        <div class="mb-5">
            <h2 class="text-center mb-4">Más Visitados en <?= htmlspecialchars($categoryName) ?></h2>
            <div class="row">
                <?php foreach (array_slice($products, 0, 4) as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/ecommerce-php/product/<?= $product['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?php 
                                    $specs = json_decode($product['specifications'], true);
                                    echo htmlspecialchars($specs['CPU'] . ' / ' . $specs['RAM']);
                                    ?>
                                </p>
                                <div class="price-section">
                                    <h4 class="text-primary mb-2">$<?= number_format($product['price'], 2) ?></h4>
                                </div>
                                <div class="mt-3">
                                    <span class="text-muted">
                                        <i class="fas fa-eye"></i> <?= $product['visits'] ?>
                                    </span>
                                    <span class="text-muted ms-2">
                                        <i class="fas fa-comment"></i> <?= $product['comment_count'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <a href="/ecommerce-php/product/<?= $product['id'] ?>" class="btn btn-outline-primary w-100">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center">
                <a href="/ecommerce-php/category/<?= array_key_first($categories) ?>" class="btn btn-link">
                    Ver más en <?= htmlspecialchars($categoryName) ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>