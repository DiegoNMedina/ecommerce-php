// Funcionalidad para la búsqueda en tiempo real
const searchProducts = () => {
    const searchInput = document.querySelector('input[name="q"]');
    if (!searchInput) return;

    let timeout = null;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = e.target.value.trim();
            if (query.length >= 3) {
                fetch(`/ecommerce-php/public_html/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Aquí se podría implementar una vista previa de resultados
                        console.log('Resultados de búsqueda:', data);
                    });
            }
        }, 500);
    });
};

// Calculadora de mensualidades
const initInstallmentCalculator = () => {
    const calculator = document.getElementById('installmentCalculator');
    if (!calculator) return;

    const calculatePayments = (price, months) => {
        fetch('/ecommerce-php/public_html/calculator', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `price=${price}&months=${months}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('monthlyPayment').textContent = 
                    data.monthlyPayment.toFixed(2);
                document.getElementById('totalAmount').textContent = 
                    data.totalAmount.toFixed(2);
                document.getElementById('calculatorResult').classList.remove('d-none');
            }
        });
    };

    calculator.addEventListener('submit', (e) => {
        e.preventDefault();
        const price = parseFloat(e.target.price.value);
        const months = parseInt(e.target.months.value);
        calculatePayments(price, months);
    });
};

// Sistema de comentarios
const initCommentSystem = () => {
    const commentForm = document.getElementById('commentForm');
    if (!commentForm) return;

    commentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(commentForm);
        const submitButton = commentForm.querySelector('button[type="submit"]');
        
        // Deshabilitar el botón mientras se envía
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';

        // Construir URL absoluta manualmente
        const baseUrl = window.location.origin + '/ecommerce-php';
        const productId = window.location.pathname.split('/').pop();
        const actionUrl = `${baseUrl}/product/${productId}/comment`;
        
        console.log('Form action URL:', commentForm.action);
        console.log('Constructed URL:', actionUrl);
        console.log('Form method:', commentForm.method);
        
        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta no es JSON válido');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Crear el nuevo comentario dinámicamente
                const commentsList = document.querySelector('.comments-list');
                const newComment = createCommentElement(
                    data.commentId,
                    formData.get('name'),
                    formData.get('comment'),
                    parseInt(formData.get('rating')),
                    new Date()
                );
                
                // Insertar el nuevo comentario al principio de la lista
                commentsList.insertBefore(newComment, commentsList.firstChild);
                
                // Limpiar el formulario
                commentForm.reset();
                
                // Mostrar mensaje de éxito
                showSuccessMessage('Comentario agregado exitosamente');
            } else {
                showErrorMessage(data.message || 'Error al enviar el comentario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Error de conexión. Inténtalo de nuevo.');
        })
        .finally(() => {
            // Rehabilitar el botón
            submitButton.disabled = false;
            submitButton.textContent = 'Enviar Comentario';
        });
    });
};

// Función para crear un elemento de comentario
const createCommentElement = (id, name, comment, rating, date) => {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'card mb-3';
    commentDiv.id = `comment-${id}`;
    
    const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
    const formattedDate = date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    commentDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="card-title">${escapeHtml(name)}</h5>
                <div class="rating text-warning">
                    ${stars}
                </div>
            </div>
            <p class="card-text">${escapeHtml(comment)}</p>
            <small class="text-muted">
                ${formattedDate}
            </small>
        </div>
    `;
    
    return commentDiv;
};

// Función para escapar HTML
const escapeHtml = (text) => {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
};

// Función para mostrar mensajes de éxito
const showSuccessMessage = (message) => {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const commentForm = document.getElementById('commentForm');
    commentForm.parentNode.insertBefore(alert, commentForm);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
};

// Función para mostrar mensajes de error
const showErrorMessage = (message) => {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const commentForm = document.getElementById('commentForm');
    commentForm.parentNode.insertBefore(alert, commentForm);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
};

// Animaciones de entrada
const initAnimations = () => {
    const animateElements = document.querySelectorAll('.card, .hero-section, .categories-section');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    });

    animateElements.forEach(element => observer.observe(element));
};

// Manejador para botones de calcular cuotas
const initCalculateButtons = () => {
    document.querySelectorAll('.calculate-installments').forEach(button => {
        button.addEventListener('click', function() {
            const price = parseFloat(this.dataset.price);
            
            if (!price || price <= 0) {
                alert('Precio no válido');
                return;
            }
            
            // Crear modal dinámico para mostrar cálculos
            const modal = createInstallmentModal(price);
            document.body.appendChild(modal);
            
            // Mostrar el modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
            // Remover el modal cuando se cierre
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
            });
        });
    });
};

// Función para crear modal de cálculo de cuotas
const createInstallmentModal = (price) => {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.tabIndex = -1;
    
    // Calcular cuotas
    const installment6 = calculateInstallment(price, 6);
    const installment12 = calculateInstallment(price, 12);
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator me-2"></i>Cálculo de Mensualidades
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h4 class="text-primary">Precio: $${price.toFixed(2)}</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">6 Meses</h5>
                                    <h3 class="text-primary">$${installment6.toFixed(2)}</h3>
                                    <p class="text-muted">por mes</p>
                                    <small class="text-muted">
                                        Total: $${(installment6 * 6).toFixed(2)}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">12 Meses</h5>
                                    <h3 class="text-primary">$${installment12.toFixed(2)}</h3>
                                    <p class="text-muted">por mes</p>
                                    <small class="text-muted">
                                        Total: $${(installment12 * 12).toFixed(2)}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Cálculo con interés anual del 10%
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    `;
    
    return modal;
};

// Función para calcular cuotas con interés
const calculateInstallment = (price, months) => {
    const annualInterest = 0.10; // 10% anual
    const monthlyInterest = annualInterest / 12;
    
    // Fórmula de cuota fija con interés compuesto
    const monthlyPayment = (price * monthlyInterest * Math.pow(1 + monthlyInterest, months)) / 
                          (Math.pow(1 + monthlyInterest, months) - 1);
    
    return monthlyPayment;
};

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    searchProducts();
    initInstallmentCalculator();
    initCommentSystem();
    initAnimations();
    initCalculateButtons();

    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Re-inicializar botones cuando se carga contenido dinámico
window.reinitCalculateButtons = () => {
    initCalculateButtons();
};

// Manejador de errores global
window.addEventListener('error', (e) => {
    console.error('Error JavaScript:', e.message);
    // Aquí se podría implementar un sistema de registro de errores
});

// Función para formatear precios
window.formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(price);
};

// Función para manejar likes
window.handleLike = (productId) => {
    const likeButton = document.querySelector(`[data-product-id="${productId}"]`);
    if (!likeButton) return;

    likeButton.addEventListener('click', () => {
        fetch(`/ecommerce-php/product/${productId}/like`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likesCount = likeButton.querySelector('.likes-count');
                if (likesCount) {
                    likesCount.textContent = parseInt(likesCount.textContent) + 1;
                }
                likeButton.classList.add('liked');
            }
        });
    });
};