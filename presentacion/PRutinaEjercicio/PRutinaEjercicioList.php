<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — EJERCICIOS DE LA RUTINA (PRutinaEjercicioList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar los ejercicios configurados para una rutina dada (?rutina_id=).
// 2. Permitir desvincular un ejercicio de la rutina.
// 3. Comunicarse únicamente con la Capa de Negocio (NRutina y NRutinaEjercicio).
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NRutinaEjercicio.php';
require_once __DIR__ . '/../../Negocio/NRutina.php';

$negocioRutinaEjercicio = new NRutinaEjercicio();
$negocioRutina          = new NRutina();

$tituloPagina = "Ejercicios de Rutina";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];
$rutinaId     = isset($_GET['rutina_id']) ? (int)$_GET['rutina_id'] : 0;

// Paso 1: Validar que la rutina exista y pertenezca al entrenador
$rutina = $negocioRutina->getById($rutinaId, $entrenadorId);
if (!$rutina) {
    header('Location: /PRutina/PRutinaList.php');
    exit();
}

$mensajeAlerta = '';
$tipoAlerta = 'info';

// Paso 2: Procesar eliminación de un ejercicio de la rutina
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultado = $negocioRutinaEjercicio->delete($idEliminar);

    if ($resultado === true) {
        $mensajeAlerta = 'Ejercicio removido de la rutina con éxito.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultado;
        $tipoAlerta = 'danger';
    }
}

// Paso 3: Obtener los ejercicios asignados a esta rutina
$listaEjercicios = $negocioRutinaEjercicio->getByRutina($rutinaId);
?>

<div class="header-section">
    <div>
        <h2>🏋️ Ejercicios de: <?= htmlspecialchars($rutina->nombre) ?></h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">
            Categoría: <strong><?= htmlspecialchars($rutina->tipo) ?></strong> — Detalle de días, series y descansos
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/PRutina/PRutinaList.php" class="btn btn-secondary">← Volver a Rutinas</a>
        <a href="PRutinaEjercicioCreate.php?rutina_id=<?= $rutinaId ?>" class="btn btn-success">+ Asignar Ejercicio</a>
    </div>
</div>

<?php if (!empty($mensajeAlerta)): ?>
    <div class="alert alert-<?= $tipoAlerta ?>">
        <?= htmlspecialchars($mensajeAlerta) ?>
    </div>
<?php endif; ?>

<div class="tabla-card">
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Día</th>
                <th>Ejercicio</th>
                <th>Series</th>
                <th>Repeticiones</th>
                <th>Duración (seg)</th>
                <th>Descanso (seg)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEjercicios)): ?>
                <tr>
                    <td colspan="8" class="sin-datos">
                        Esta rutina aún no tiene ejercicios asignados. ¡Haz clic en "+ Asignar Ejercicio" para armar el plan!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($listaEjercicios as $ej): ?>
                    <tr>
                        <td><?= $ej->id ?></td>
                        <td>
                            <span class="badge badge-secondary" style="font-weight: 700;">
                                📅 <?= htmlspecialchars($ej->dia_rutina) ?>
                            </span>
                        </td>
                        <td><strong><?= htmlspecialchars($ej->ejercicio_nombre) ?></strong></td>
                        <td><?= (int)$ej->series ?> series</td>
                        <td><?= (int)$ej->repeticiones ?> reps</td>
                        <td><?= (int)$ej->duracion ?> s</td>
                        <td><?= (int)$ej->descanso ?> s</td>
                        <td>
                            <a href="PRutinaEjercicioList.php?rutina_id=<?= $rutinaId ?>&delete_id=<?= $ej->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Remover este ejercicio de la rutina?');">
                               🗑️ Quitar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
