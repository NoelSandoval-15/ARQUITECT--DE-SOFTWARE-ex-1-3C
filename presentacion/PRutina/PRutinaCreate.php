<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — CREAR RUTINA (PRutinaCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar el formulario para registrar una nueva rutina.
// 2. Asociarla al entrenador autenticado.
// 3. Invocar la Capa de Negocio (NRutina).
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NRutina.php';

$mensajeError = '';

// Paso 1: Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Nueva Rutina";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 2: Procesar formulario si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo   = trim($_POST['tipo'] ?? '');

    $negocioRutina = new NRutina();
    $resultado = $negocioRutina->create($entrenadorId, $nombre, $tipo);

    if ($resultado === true) {
        header('Location: PRutinaList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}
?>

<div class="form-card">
    <h2>📋 Crear Nueva Rutina</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PRutinaCreate.php">

        <div class="form-group">
            <label for="nombre">Nombre de la Rutina *</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                   placeholder="Ej: Rutina Pierna y Glúteo, Full Body A..." required autofocus>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo / Categoría de Enfoque *</label>
            <select id="tipo" name="tipo" required>
                <option value="">-- Seleccionar Tipo --</option>
                <?php
                $tipos = ['Fuerza', 'Hipertrofia', 'Cardio', 'Flexibilidad', 'Resistencia', 'Mixta'];
                $tipoSeleccionado = $_POST['tipo'] ?? '';
                foreach ($tipos as $t):
                ?>
                    <option value="<?= $t ?>" <?= ($tipoSeleccionado === $t) ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="PRutinaList.php" class="btn btn-secondary">← Volver al Listado</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Guardar Rutina</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
