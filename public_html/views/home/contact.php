<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>Contáctanos
                </h2>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo pronto.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="contact-info">
                            <h4 class="mb-3">Información de Contacto</h4>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    123 Calle Principal, Ciudad
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    +1 234 567 890
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    info@computerstore.com
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    Lunes a Viernes: 9:00 AM - 6:00 PM
                                </li>
                            </ul>
                            <div class="social-links mt-4">
                                <a href="#" class="btn btn-outline-primary me-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary me-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary me-2">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="map-container">
                            <!-- Aquí se podría integrar un mapa de Google Maps -->
                            <div class="ratio ratio-4x3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-map fa-3x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="/ecommerce-php/contact" method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">
                                Por favor ingresa tu nombre
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">
                                Por favor ingresa un correo electrónico válido
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="subject" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                            <div class="invalid-feedback">
                                Por favor ingresa el asunto
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            <div class="invalid-feedback">
                                Por favor ingresa tu mensaje
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- FAQs -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h3 class="mb-0">Preguntas Frecuentes</h3>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h4 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                ¿Cuáles son los métodos de pago aceptados?
                            </button>
                        </h4>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Aceptamos todas las tarjetas principales (Visa, MasterCard, American Express) y ofrecemos planes de financiamiento a 6 y 12 meses sin intereses.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h4 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                ¿Cuál es el tiempo de entrega?
                            </button>
                        </h4>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                El tiempo de entrega estándar es de 3 a 5 días hábiles. Para zonas remotas puede tomar hasta 7 días hábiles.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h4 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                ¿Tienen garantía los productos?
                            </button>
                        </h4>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Todos nuestros productos cuentan con garantía del fabricante por un mínimo de 12 meses.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>