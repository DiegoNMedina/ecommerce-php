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

        fetch(commentForm.action, {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
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

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    searchProducts();
    initInstallmentCalculator();
    initCommentSystem();
    initAnimations();

    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

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
        fetch(`/ecommerce-php/public_html/product/${productId}/like`, {
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