<?php 
require_once 'controllers/csosal.php'; 
?>

<div class="conte">
    <h2><i class="fa-solid fa-arrow-up-from-bracket"></i> Detalle de Salida</h2>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php 
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        ?>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Agregar Producto a Salida</h5>
        </div>
        <div class="card-body">
            <form action="dashboard.php?pg=2070&idsol=<?= htmlspecialchars($idsol) ?>" method="POST" id="formDetalle">
                <input type="hidden" name="ope" value="save">
                <input type="hidden" name="idsol" value="<?= htmlspecialchars($idsol) ?>">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="idprod" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select name="idprod" id="idprod" class="form-control" required onchange="mostrarStock()">
                            <option value="">Seleccione un producto...</option>
                            <?php 
                            if (isset($productos) && is_array($productos) && !empty($productos)) {
                                foreach ($productos as $p):
                            ?>
                                    <option value="<?= htmlspecialchars($p['idprod']) ?>" 
                                            data-stock="<?= isset($p['stock']) ? $p['stock'] : 0 ?>"
                                            data-precio="<?= isset($p['precio']) ? $p['precio'] : 0 ?>">
                                        <?= htmlspecialchars($p['nomprod']) ?>
                                        <?php if (isset($p['stock'])): ?>
                                            (Stock: <?= $p['stock'] ?>)
                                        <?php endif; ?>
                                    </option>
                            <?php 
                                endforeach;
                            } else {
                                echo '<option disabled>No hay productos disponibles</option>';
                            }
                            ?>
                        </select>
                        <div id="stock-info" class="form-text"></div>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="cantdet" class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="cantdet" id="cantdet" class="form-control" min="1" step="1" required>
                        <div class="form-text" id="stock-warning" style="color: red; display: none;">
                            Cantidad mayor al stock disponible
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="vundet" class="form-label">Valor Unitario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="vundet" id="vundet" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="total_preview" class="form-label">Total</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="total_preview" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning" id="btnGuardar">
                            <i class="fa-solid fa-save"></i> Guardar Salida
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fa-solid fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr>

    <!-- Lista de detalles -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Productos en Salida</h5>
            <?php if (isset($detalles) && count($detalles) > 0): ?>
                <span class="badge bg-warning"><?= count($detalles) ?> productos</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (isset($detalles) && is_array($detalles) && count($detalles) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Valor Unitario</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $gran_total = 0;
                            foreach ($detalles as $d): 
                                $gran_total += $d['totdet'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nomprod']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($d['cantdet']) ?></td>
                                <td class="text-end">$<?= number_format($d['vundet'], 2, ',', '.') ?></td>
                                <td class="text-end">$<?= number_format($d['totdet'], 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="dashboard.php?pg=2070&idsol=<?= $idsol ?>&delete=<?= $d['iddet'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('¿Está seguro de eliminar este registro?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="3" class="text-end">TOTAL GENERAL:</th>
                                <th class="text-end">$<?= number_format($gran_total, 2, ',', '.') ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay productos registrados en esta salida</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cantidadInput = document.getElementById('cantdet');
    const valorInput = document.getElementById('vundet');
    const totalPreview = document.getElementById('total_preview');
    const productoSelect = document.getElementById('idprod');
    const stockInfo = document.getElementById('stock-info');
    const stockWarning = document.getElementById('stock-warning');
    const btnGuardar = document.getElementById('btnGuardar');
    
    function calcularTotal() {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const valor = parseFloat(valorInput.value) || 0;
        const total = cantidad * valor;
        totalPreview.value = total.toFixed(2);
    }
    
    function mostrarStock() {
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.stock !== undefined) {
            const stock = parseInt(selectedOption.dataset.stock);
            const precio = parseFloat(selectedOption.dataset.precio);
            
            stockInfo.textContent = `Stock disponible: ${stock} unidades`;
            stockInfo.style.color = stock > 0 ? 'green' : 'red';
            
            // Prellenar precio si está disponible
            if (precio > 0 && !valorInput.value) {
                valorInput.value = precio.toFixed(2);
                calcularTotal();
            }
            
            // Validar cantidad vs stock
            validarStock();
        } else {
            stockInfo.textContent = '';
        }
    }
    
    function validarStock() {
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.stock !== undefined) {
            const stock = parseInt(selectedOption.dataset.stock);
            const cantidad = parseInt(cantidadInput.value) || 0;
            
            if (cantidad > stock) {
                stockWarning.style.display = 'block';
                btnGuardar.disabled = true;
            } else {
                stockWarning.style.display = 'none';
                btnGuardar.disabled = false;
            }
        }
    }
    
    cantidadInput.addEventListener('input', function() {
        calcularTotal();
        validarStock();
    });
    
    valorInput.addEventListener('input', calcularTotal);
    productoSelect.addEventListener('change', mostrarStock);
    
    // Limpiar el preview al resetear
    document.getElementById('formDetalle').addEventListener('reset', function() {
        totalPreview.value = '';
        stockInfo.textContent = '';
        stockWarning.style.display = 'none';
        btnGuardar.disabled = false;
    });
    
    // Hacer la función global para que funcione desde el HTML
    window.mostrarStock = mostrarStock;
});
</script>