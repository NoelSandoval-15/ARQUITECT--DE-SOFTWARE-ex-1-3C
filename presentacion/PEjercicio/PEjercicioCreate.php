<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — CREAR EJERCICIO (PEjercicioCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar el formulario para registrar un nuevo ejercicio.
// 2. Recibir los datos vía POST.
// 3. Delegar la validación y guardado a la Capa de Negocio (NEjercicio).
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NEjercicio.php';

$mensajeError = '';

// Paso 1: Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = trim($_POST['nombre'] ?? '');
    $duracion   = trim($_POST['duracion'] ?? '');
    $repeticion = trim($_POST['repeticion'] ?? '');
    $imagen_url = trim($_POST['imagen_url'] ?? '');
    $video_url  = trim($_POST['video_url'] ?? '');

    // Paso 2: Invocar Capa de Negocio
    $negocioEjercicio = new NEjercicio();
    $resultado = $negocioEjercicio->create($nombre, $duracion, $repeticion, $imagen_url, $video_url);

    if ($resultado === true) {
        header('Location: PEjercicioList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}

// Paso 3: Renderizar cabecera común
$tituloPagina = "Nuevo Ejercicio";
require_once __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>➕ Registrar Nuevo Ejercicio</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PEjercicioCreate.php">

        <div class="form-group">
            <label for="nombre">Nombre del Ejercicio *</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                   placeholder="Ej: Press de banca, Sentadilla libre..." required autofocus>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="duracion">Duración / Tiempo estimado</label>
                <input type="text" id="duracion" name="duracion" 
                       value="<?= htmlspecialchars($_POST['duracion'] ?? '') ?>" 
                       placeholder="Ej: 45 seg, 2 min...">
            </div>
            <div class="form-group">
                <label for="repeticion">Repeticiones / Series *</label>
                <input type="text" id="repeticion" name="repeticion" 
                       value="<?= htmlspecialchars($_POST['repeticion'] ?? '') ?>" 
                       placeholder="Ej: 4 series de 12 reps" required>
            </div>
        </div>

        <div class="form-group">
            <label for="imagen_url">URL de Imagen Ilustrativa (opcional)</label>
            <input type="url" id="imagen_url" name="imagen_url" 
                   value="<?= htmlspecialchars($_POST['imagen_url'] ?? '') ?>" 
                   placeholder="https://ejemplo.com/imagen.jpg">
        </div>

        <div class="form-group">
            <label for="video_url">URL de Video Demostrativo (YouTube / Web) (opcional)</label>
            <input type="url" id="video_url" name="video_url" 
                   value="<?= htmlspecialchars($_POST['video_url'] ?? '') ?>" 
                   placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div class="form-actions">
            <a href="PEjercicioList.php" class="btn btn-secondary">← Volver al Catálogo</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Guardar Ejercicio</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
