<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — EDITAR RUTINA (PRutinaEdit.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Obtener la rutina existente del entrenador mediante NRutina.
// 2. Precargar el formulario para edición.
// 3. Procesar los cambios enviados vía POST.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NRutina.php';

$negocioRutina = new NRutina();
$mensajeError  = '';

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Editar Rutina";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 1: Obtener el ID de la rutina
$idRutina = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$rutina   = $negocioRutina->getById($idRutina, $entrenadorId);

if (!$rutina) {
    header('Location: PRutinaList.php');
    exit();
}

// Paso 2: Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo   = trim($_POST['tipo'] ?? '');

    $resultado = $negocioRutina->update($idRutina, $nombre, $tipo);

    if ($resultado === true) {
        header('Location: PRutinaList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}
?>

<div class="form-card">
    <h2>✏️ Editar Rutina</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PRutinaEdit.php?id=<?= $idRutina ?>">

        <div class="form-group">
            <label for="nombre">Nombre de la Rutina *</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? $rutina->nombre) ?>" 
                   required autofocus>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo / Categoría de Enfoque *</label>
            <?php
            $tipos = ['Fuerza', 'Hipertrofia', 'Cardio', 'Flexibilidad', 'Resistencia', 'Mixta'];
            $tipoActual = $_POST['tipo'] ?? $rutina->tipo;
            ?>
            <select id="tipo" name="tipo" required>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t ?>" <?= ($tipoActual === $t) ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="PRutinaList.php" class="btn btn-secondary">← Volver al Listado</a>
            <button type="submit" class="btn btn-warning" style="flex: 1;">💾 Actualizar Rutina</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
