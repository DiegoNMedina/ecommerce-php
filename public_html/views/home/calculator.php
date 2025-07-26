<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>Calculadora de Mensualidades
                </h2>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Calcula tus pagos mensuales con un interés anual del 10%
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Disponible a 6 y 12 meses sin intereses
                        </div>
                    </div>
                </div>

                <form id="installmentCalculator" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Precio del Producto ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price"
                                    min="1" step="0.01" required>
                                <div class="invalid-feedback">
                                    Por favor ingresa un precio válido
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="months" class="form-label">Plazo en Meses</label>
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

                <div id="calculatorResult" class="mt-4 d-none">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Resultado del Cálculo</h3>

                            <div class="row text-center">
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded bg-white">
                                        <h4 class="text-primary mb-2">Pago Mensual</h4>
                                        <p class="h2 mb-0">$<span id="monthlyPayment">0.00</span></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded bg-white">
                                        <h4 class="text-primary mb-2">Total a Pagar</h4>
                                        <p class="h2 mb-0">$<span id="totalAmount">0.00</span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Los montos mostrados son aproximados y pueden variar según la fecha de compra.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h5>Pagos Seguros</h5>
                        <p class="mb-0">Todas las transacciones son procesadas de manera segura</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                        <h5>Sin Intereses</h5>
                        <p class="mb-0">Aprovecha nuestros planes a meses sin intereses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                        <h5>Múltiples Tarjetas</h5>
                        <p class="mb-0">Aceptamos todas las tarjetas principales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('installmentCalculator');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>