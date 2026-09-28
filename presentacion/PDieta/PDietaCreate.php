<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — CREAR DIETA (PDietaCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar el formulario para registrar una nueva dieta.
// 2. Asociarla al entrenador autenticado.
// 3. Invocar la Capa de Negocio (NDieta).
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NDieta.php';

$mensajeError = '';

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Nueva Dieta";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 1: Procesar envío de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
 
    $negocioDieta = new NDieta();
    $resultado = $negocioDieta->create($entrenadorId, $titulo, $descripcion);

    if ($resultado === true) {
        header('Location: PDietaList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}
?>

<div class="form-card">
    <h2>🥗 Crear Nueva Dieta</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PDietaCreate.php">

        <div class="form-group">
            <label for="titulo">Título de la Dieta *</label>
            <input type="text" id="titulo" name="titulo" 
                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" 
                   placeholder="Ej: Dieta Hipercalórica 2800 kcal, Déficit Calórico Moderado..." required autofocus>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción y Guía de Alimentación *</label>
            <textarea id="descripcion" name="descripcion" rows="6" 
                      placeholder="Indica alimentos recomendados, distribución de macronutrientes, comidas sugeridas..." required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="PDietaList.php" class="btn btn-secondary">← Volver al Listado</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Guardar Dieta</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
