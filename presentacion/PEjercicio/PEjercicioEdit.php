<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — EDITAR EJERCICIO (PEjercicioEdit.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Cargar el ejercicio existente mediante NEjercicio.
// 2. Renderizar el formulario con los datos actuales.
// 3. Procesar la actualización vía POST enviando a la Capa de Negocio.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NEjercicio.php';

$negocioEjercicio = new NEjercicio();
$mensajeError     = '';

// Paso 1: Obtener el ID del ejercicio
$idEjercicio = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ejercicio   = $negocioEjercicio->getById($idEjercicio);

if (!$ejercicio) {
    header('Location: PEjercicioList.php');
    exit();
}

// Paso 2: Procesar actualización al enviar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = trim($_POST['nombre'] ?? '');
    $duracion   = trim($_POST['duracion'] ?? '');
    $repeticion = trim($_POST['repeticion'] ?? '');
    $imagen_url = trim($_POST['imagen_url'] ?? '');
    $video_url  = trim($_POST['video_url'] ?? '');

    $resultado = $negocioEjercicio->update($idEjercicio, $nombre, $duracion, $repeticion, $imagen_url, $video_url);

    if ($resultado === true) {
        header('Location: PEjercicioList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}

// Paso 3: Renderizar cabecera común
$tituloPagina = "Editar Ejercicio #" . $idEjercicio;
require_once __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>✏️ Editar Ejercicio</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PEjercicioEdit.php?id=<?= $idEjercicio ?>">

        <div class="form-group">
            <label for="nombre">Nombre del Ejercicio *</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? $ejercicio->nombre) ?>" 
                   required autofocus>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="duracion">Duración / Tiempo estimado</label>
                <input type="text" id="duracion" name="duracion" 
                       value="<?= htmlspecialchars($_POST['duracion'] ?? $ejercicio->duracion) ?>">
            </div>
            <div class="form-group">
                <label for="repeticion">Repeticiones / Series *</label>
                <input type="text" id="repeticion" name="repeticion" 
                       value="<?= htmlspecialchars($_POST['repeticion'] ?? $ejercicio->repeticion) ?>" 
                       required>
            </div>
        </div>

        <div class="form-group">
            <label for="imagen_url">URL de Imagen Ilustrativa (opcional)</label>
            <input type="url" id="imagen_url" name="imagen_url" 
                   value="<?= htmlspecialchars($_POST['imagen_url'] ?? $ejercicio->imagen_url) ?>">
        </div>

        <div class="form-group">
            <label for="video_url">URL de Video Demostrativo (YouTube / Web) (opcional)</label>
            <input type="url" id="video_url" name="video_url" 
                   value="<?= htmlspecialchars($_POST['video_url'] ?? $ejercicio->video_url) ?>">
        </div>

        <div class="form-actions">
            <a href="PEjercicioList.php" class="btn btn-secondary">← Volver al Catálogo</a>
            <button type="submit" class="btn btn-warning" style="flex: 1;">💾 Actualizar Ejercicio</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
