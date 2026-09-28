<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — ASIGNAR EJERCICIO A RUTINA (PRutinaEjercicioCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Cargar el catálogo de ejercicios mediante NEjercicio.
// 2. Mostrar el formulario con los parámetros (día, series, reps, descanso).
// 3. Procesar el formulario vía POST e invocar NRutinaEjercicio.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NRutinaEjercicio.php';
require_once __DIR__ . '/../../Negocio/NRutina.php';
require_once __DIR__ . '/../../Negocio/NEjercicio.php';

$negocioRutinaEjercicio = new NRutinaEjercicio();
$negocioRutina          = new NRutina();
$negocioEjercicio       = new NEjercicio();

$tituloPagina = "Asignar Ejercicio";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];
$rutinaId     = isset($_GET['rutina_id']) ? (int)$_GET['rutina_id'] : 0;

// Validar que la rutina exista y pertenezca al entrenador
$rutina = $negocioRutina->getById($rutinaId, $entrenadorId);
if (!$rutina) {
    header('Location: /PRutina/PRutinaList.php');
    exit();
}

$mensajeError = '';

// Paso 1: Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ejercicio_id = (int)($_POST['ejercicio_id'] ?? 0);
    $dia_rutina   = trim($_POST['dia_rutina'] ?? '');
    $series       = (int)($_POST['series'] ?? 0);
    $repeticiones = (int)($_POST['repeticiones'] ?? 0);
    $duracion     = (int)($_POST['duracion'] ?? 0);
    $descanso     = (int)($_POST['descanso'] ?? 0);

    $resultado = $negocioRutinaEjercicio->create(
        $rutinaId, $ejercicio_id, $dia_rutina,
        $series, $repeticiones, $duracion, $descanso
    );

    if ($resultado === true) {
        header("Location: PRutinaEjercicioList.php?rutina_id={$rutinaId}");
        exit();
    } else {
        $mensajeError = $resultado;
    }
}

// Paso 2: Obtener ejercicios para el selector desplegable
$catalogoEjercicios = $negocioEjercicio->getAll();
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>

<div class="form-card" style="max-width: 650px;">
    <h2>➕ Asignar Ejercicio a: <?= htmlspecialchars($rutina->nombre) ?></h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PRutinaEjercicioCreate.php?rutina_id=<?= $rutinaId ?>">

        <div class="form-row-2">
            <div class="form-group">
                <label for="ejercicio_id">Ejercicio del Catálogo *</label>
                <select id="ejercicio_id" name="ejercicio_id" required autofocus>
                    <option value="">-- Seleccionar Ejercicio --</option>
                    <?php foreach ($catalogoEjercicios as $ej): ?>
                        <option value="<?= $ej->id ?>" <?= (($_POST['ejercicio_id'] ?? '') == $ej->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ej->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dia_rutina">Día Asignado *</label>
                <select id="dia_rutina" name="dia_rutina" required>
                    <option value="">-- Seleccionar Día --</option>
                    <?php foreach ($diasSemana as $dia): ?>
                        <option value="<?= $dia ?>" <?= (($_POST['dia_rutina'] ?? '') === $dia) ? 'selected' : '' ?>>
                            <?= $dia ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="series">Series (ej: 4) *</label>
                <input type="number" id="series" name="series" min="1" max="20"
                       value="<?= htmlspecialchars($_POST['series'] ?? '4') ?>" required>
            </div>

            <div class="form-group">
                <label for="repeticiones">Repeticiones por serie (ej: 12) *</label>
                <input type="number" id="repeticiones" name="repeticiones" min="1" max="100"
                       value="<?= htmlspecialchars($_POST['repeticiones'] ?? '12') ?>" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="duracion">Duración en Segundos (opcional)</label>
                <input type="number" id="duracion" name="duracion" min="0" step="5"
                       value="<?= htmlspecialchars($_POST['duracion'] ?? '0') ?>" 
                       placeholder="Ej: 60 para planchas">
            </div>

            <div class="form-group">
                <label for="descanso">Descanso en Segundos *</label>
                <input type="number" id="descanso" name="descanso" min="0" step="5"
                       value="<?= htmlspecialchars($_POST['descanso'] ?? '60') ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="PRutinaEjercicioList.php?rutina_id=<?= $rutinaId ?>" class="btn btn-secondary">← Cancelar</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Asignar a la Rutina</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
