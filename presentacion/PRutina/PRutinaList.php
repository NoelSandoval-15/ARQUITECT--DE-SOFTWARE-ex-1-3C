<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — LISTA DE RUTINAS (PRutinaList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar las rutinas creadas por el entrenador logueado.
// 2. Permitir eliminar una rutina delegando a la Capa de Negocio (NRutina).
// 3. Ofrecer enlaces directos a la gestión de ejercicios de cada rutina.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NRutina.php';

$negocioRutina = new NRutina();
$mensajeAlerta = '';
$tipoAlerta = 'info';

// Paso 1: Procesar eliminación si viene 'delete_id' por GET
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultado = $negocioRutina->delete($idEliminar);

    if ($resultado === true) {
        $mensajeAlerta = 'Rutina eliminada con éxito.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultado;
        $tipoAlerta = 'danger';
    }
}

// Iniciar sesión y validar entrenador mediante el header común
$tituloPagina = "Gestión de Rutinas";
require_once __DIR__ . '/../layout/header.php';

// Paso 2: Obtener las rutinas del entrenador desde la Capa de Negocio
$idEntrenador = (int)$_SESSION['entrenador_id'];
$listaRutinas = $negocioRutina->getAll($idEntrenador);
?>

<div class="header-section">
    <div>
        <h2>📋 Rutinas de Entrenamiento</h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">Planifica rutinas personalizadas organizadas por tipo y ejercicios</p>
    </div>
    <a href="PRutinaCreate.php" class="btn btn-success">+ Nueva Rutina</a>
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
                <th style="width: 60px;">#</th>
                <th>Nombre de la Rutina</th>
                <th>Enfoque / Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaRutinas)): ?>
                <tr>
                    <td colspan="4" class="sin-datos">
                        Aún no tienes rutinas registradas. ¡Crea la primera con el botón superior!
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                    // PAGINACIÓN (Lógica exclusiva de Presentación)
                    $porPagina = 5;
                    $totalRegistros = count($listaRutinas);
                    $totalPaginas = ceil($totalRegistros / $porPagina);
                    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    if ($paginaActual < 1) $paginaActual = 1;
                    if ($paginaActual > $totalPaginas && $totalPaginas > 0) $paginaActual = $totalPaginas;
                    
                    $offset = ($paginaActual - 1) * $porPagina;
                    $listaPaginada = array_slice($listaRutinas, $offset, $porPagina);
                ?>
                <?php foreach ($listaPaginada as $rutina): ?>
                    <tr>
                        <td><?= $rutina->id ?></td>
                        <td>
                            <strong><?= htmlspecialchars($rutina->nombre) ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-info" style="font-size: 12px;">
                                🎯 <?= htmlspecialchars($rutina->tipo) ?>
                            </span>
                        </td>
                        <td>
                            <a href="/PRutinaEjercicio/PRutinaEjercicioList.php?rutina_id=<?= $rutina->id ?>" 
                               class="btn btn-primary btn-sm" title="Ver y asignar ejercicios">
                               🏋️ Ejercicios
                            </a>
                            <a href="PRutinaEdit.php?id=<?= $rutina->id ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <a href="PRutinaList.php?delete_id=<?= $rutina->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar <?= htmlspecialchars($rutina->nombre) ?> y sus ejercicios asignados?');">
                               🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if (!empty($totalPaginas) && $totalPaginas > 1): ?>
    <div style="margin-top: 15px; display: flex; justify-content: center; gap: 5px; padding: 10px 0;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>" class="btn <?= $i === $paginaActual ? 'btn-primary' : 'btn-secondary' ?> btn-sm" style="min-width: 30px; text-align: center;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
