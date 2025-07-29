<?php
$specs = json_decode($product['specifications'], true);
$categories = explode(',', $product['categories']);
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/ecommerce-php/">Inicio</a></li>
        <li class="breadcrumb-item"><a href="/ecommerce-php/products">Productos</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?></li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="product-image-gallery">
            <img src="/ecommerce-php/assets/img/products/default.svg" class="img-fluid rounded" 
                 alt="<?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?>">
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <h1 class="mb-3"><?= htmlspecialchars($product['brand'] . ' ' . $product['model']) ?></h1>
        
        <div class="categories mb-3">
            <?php foreach ($categories as $category): ?>
                <span class="badge bg-secondary me-1"><?= htmlspecialchars(trim($category)) ?></span>
            <?php endforeach; ?>
        </div>

        <div class="product-stats mb-3">
            <span class="me-3">
                <i class="fas fa-eye"></i> <?= $product['visits'] ?> visitas
            </span>
            <span class="me-3">
                <i class="fas fa-thumbs-up"></i> 
                <span id="likesCount"><?= $product['likes'] ?></span> likes
            </span>
            <?php if (isset($product['avg_rating'])): ?>
                <span>
                    <i class="fas fa-star text-warning"></i> 
                    <?= number_format($product['avg_rating'], 1) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="like-section mb-3">
            <button type="button" class="btn btn-outline-primary" id="likeBtn" data-product-id="<?= $product['id'] ?>">
                <i class="fas fa-heart"></i> Me gusta
            </button>
        </div>

        <div class="price-section mb-4">
            <h2 class="text-primary mb-2">$<?= number_format($product['price'], 2) ?></h2>
            <div class="installments">
                <h5>Paga en mensualidades:</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>6 meses</h6>
                                <p class="h4 text-primary mb-0">$<?= number_format($installments[6], 2) ?></p>
                                <small class="text-muted">mensuales</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>12 meses</h6>
                                <p class="h4 text-primary mb-0">$<?= number_format($installments[12], 2) ?></p>
                                <small class="text-muted">mensuales</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="specifications mb-4">
            <h3>Especificaciones</h3>
            <table class="table">
                <tbody>
                    <?php foreach ($specs as $key => $value): ?>
                        <tr>
                            <th class="w-25"><?= htmlspecialchars($key) ?></th>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="stock-info mb-4">
            <p class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                <i class="fas <?= $product['stock'] > 0 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                <?= $product['stock'] > 0 ? 'En stock' : 'Agotado' ?>
                <?php if ($product['stock'] > 0): ?>
                    (<?= $product['stock'] ?> unidades disponibles)
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.getElementById('likeBtn');
    const likesCount = document.getElementById('likesCount');
    
    likeBtn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        
        // Deshabilitar el botón temporalmente
        likeBtn.disabled = true;
        
        fetch(`/ecommerce-php/product/${productId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar el contador de likes
                likesCount.textContent = data.likes;
                
                // Cambiar el estilo del botón para indicar que se dio like
                likeBtn.classList.remove('btn-outline-primary');
                likeBtn.classList.add('btn-primary');
                likeBtn.innerHTML = '<i class="fas fa-heart"></i> ¡Te gusta!';
                
                // Mostrar mensaje de éxito
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                likeBtn.parentNode.appendChild(alert);
                
                // Remover la alerta después de 3 segundos
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 3000);
            } else {
                // Mostrar mensaje de error
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                likeBtn.parentNode.appendChild(alert);
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
            alert.innerHTML = `
                Error al procesar la solicitud. Inténtalo de nuevo.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            likeBtn.parentNode.appendChild(alert);
            
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 3000);
        })
        .finally(() => {
            // Rehabilitar el botón después de 2 segundos
            setTimeout(() => {
                likeBtn.disabled = false;
            }, 2000);
        });
    });
});
</script>

<div class="row mb-5">
    <div class="col-md-8">
        <div class="comments-section">
            <h3>Comentarios</h3>
            
            <div class="add-comment mb-4">
                <form action="/ecommerce-php/product/<?= $product['id'] ?>/comment" method="POST" 
                      id="commentForm" class="card card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Calificación</label>
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="">Selecciona una calificación</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= str_repeat('★', $i) . str_repeat('☆', 5 - $i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Comentario</button>
                </form>
            </div>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3" id="comment-<?= $comment['id'] ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="card-title"><?= htmlspecialchars($comment['name']) ?></h5>
                                <div class="rating text-warning">
                                    <?= str_repeat('★', $comment['rating']) . str_repeat('☆', 5 - $comment['rating']) ?>
                                </div>
                            </div>
                            <p class="card-text"><?= htmlspecialchars($comment['comment']) ?></p>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="related-products">
            <h3>Productos Relacionados</h3>
            <?php foreach ($relatedProducts as $related): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="/ecommerce-php/product/<?= $related['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($related['brand'] . ' ' . $related['model']) ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php 
                            $relatedSpecs = json_decode($related['specifications'], true);
                            echo htmlspecialchars($relatedSpecs['CPU'] . ' / ' . $relatedSpecs['RAM']);
                            ?>
                        </p>
                        <div class="price-section">
                            <h4 class="text-primary">$<?= number_format($related['price'], 2) ?></h4>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>