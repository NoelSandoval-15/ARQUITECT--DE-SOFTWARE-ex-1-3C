<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — EDITAR DIETA (PDietaEdit.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Cargar la dieta existente del entrenador mediante NDieta.
// 2. Precargar el formulario con titulo y descripcion.
// 3. Procesar los cambios vía POST.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NDieta.php';

$negocioDieta = new NDieta();
$mensajeError = '';

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Editar Dieta";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 1: Obtener ID y cargar datos
$idDieta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dieta   = $negocioDieta->getById($idDieta, $entrenadorId);

if (!$dieta) {
    header('Location: PDietaList.php');
    exit();
}

// Paso 2: Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $resultado = $negocioDieta->update($idDieta, $titulo, $descripcion);

    if ($resultado === true) {
        header('Location: PDietaList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}
?>

<div class="form-card">
    <h2>✏️ Editar Dieta</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PDietaEdit.php?id=<?= $idDieta ?>">

        <div class="form-group">
            <label for="titulo">Título de la Dieta *</label>
            <input type="text" id="titulo" name="titulo" 
                   value="<?= htmlspecialchars($_POST['titulo'] ?? $dieta->titulo) ?>" 
                   required autofocus>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción y Guía de Alimentación *</label>
            <textarea id="descripcion" name="descripcion" rows="6" required><?= htmlspecialchars($_POST['descripcion'] ?? $dieta->descripcion) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="PDietaList.php" class="btn btn-secondary">← Volver al Listado</a>
            <button type="submit" class="btn btn-warning" style="flex: 1;">💾 Actualizar Dieta</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
