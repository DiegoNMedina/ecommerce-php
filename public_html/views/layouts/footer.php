</main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="/ecommerce-php/products" class="text-light">Productos</a></li>
                        <li><a href="/ecommerce-php/featured" class="text-light">Destacados</a></li>
                        <li><a href="/ecommerce-php/calculator" class="text-light">Calculadora de Pagos</a></li>
                        <li><a href="/ecommerce-php/about" class="text-light">Acerca de</a></li>
                        <li><a href="/ecommerce-php/contact" class="text-light">Contacto</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h5>Métodos de Pago</h5>
                    <ul class="list-unstyled">
                        <li><i class="fab fa-cc-visa me-2"></i>Visa</li>
                        <li><i class="fab fa-cc-mastercard me-2"></i>Mastercard</li>
                        <li><i class="fab fa-cc-amex me-2"></i>American Express</li>
                        <li><i class="fas fa-credit-card me-2"></i>Meses sin intereses</li>
                    </ul>
                </div>

                <div class="col-md-4 mb-3">
                    <h5>Contáctanos</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>+1 234 567 890</li>
                        <li><i class="fas fa-envelope me-2"></i>info@computerstore.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Calle Principal</li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?= date('Y') ?> Computer Store. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-light me-3">Términos y Condiciones</a>
                    <a href="#" class="text-light">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="/ecommerce-php/public_html/assets/js/main.js"></script>

    <!-- Calculadora de mensualidades -->
    <script>
        $(document).ready(function() {
            $('#calculatorForm').on('submit', function(e) {
                e.preventDefault();
                $.post('/ecommerce-php/calculator', $(this).serialize(), function(response) {
                    if (response.success) {
                        $('#monthlyPayment').text(response.monthlyPayment.toFixed(2));
                        $('#totalAmount').text(response.totalAmount.toFixed(2));
                        $('#calculatorResult').removeClass('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>