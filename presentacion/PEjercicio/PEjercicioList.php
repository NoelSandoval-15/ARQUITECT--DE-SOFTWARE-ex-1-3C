<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — LISTA DE EJERCICIOS (PEjercicioList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Recibir solicitudes del usuario (listar o eliminar ejercicios).
// 2. Comunicarse ÚNICAMENTE con la Capa de Negocio (NEjercicio).
// 3. Renderizar el catálogo de ejercicios con imágenes y videos.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NEjercicio.php';

$negocioEjercicio = new NEjercicio();
$mensajeAlerta = '';
$tipoAlerta = 'info';

// Paso 1: Procesar eliminación si se recibe el ID por GET
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultado = $negocioEjercicio->delete($idEliminar);

    if ($resultado === true) {
        $mensajeAlerta = 'Ejercicio eliminado correctamente.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultado;
        $tipoAlerta = 'danger';
    }
}

// Paso 2: Obtener todos los ejercicios desde la Capa de Negocio
$listaEjercicios = $negocioEjercicio->getAll();

// Paso 3: Renderizar cabecera común
$tituloPagina = "Catálogo de Ejercicios";
require_once __DIR__ . '/../layout/header.php';
?>

<div class="header-section">
    <div>
        <h2>💪 Catálogo de Ejercicios</h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">Ejercicios disponibles para armar rutinas de entrenamiento</p>
    </div>
    <a href="PEjercicioCreate.php" class="btn btn-success">+ Registrar Nuevo Ejercicio</a>
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
                <th>Nombre del Ejercicio</th>
                <th>Duración</th>
                <th>Repeticiones / Series</th>
                <th>Recursos Multimedia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEjercicios)): ?>
                <tr>
                    <td colspan="6" class="sin-datos">
                        No hay ejercicios registrados en el catálogo aún.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                    // PAGINACIÓN (Lógica exclusiva de Presentación)
                    $porPagina = 5;
                    $totalRegistros = count($listaEjercicios);
                    $totalPaginas = ceil($totalRegistros / $porPagina);
                    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    if ($paginaActual < 1) $paginaActual = 1;
                    if ($paginaActual > $totalPaginas && $totalPaginas > 0) $paginaActual = $totalPaginas;
                    
                    $offset = ($paginaActual - 1) * $porPagina;
                    $listaPaginada = array_slice($listaEjercicios, $offset, $porPagina);
                ?>
                <?php foreach ($listaPaginada as $ejercicio): ?>
                    <tr>
                        <td><?= $ejercicio->id ?></td>
                        <td>
                            <strong><?= htmlspecialchars($ejercicio->nombre) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($ejercicio->duracion) ?></td>
                        <td><?= htmlspecialchars($ejercicio->repeticion) ?></td>
                        <td>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <?php if (!empty($ejercicio->imagen_url)): ?>
                                    <a href="<?= htmlspecialchars($ejercicio->imagen_url) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Ver Imagen">
                                        🖼️ Imagen
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($ejercicio->video_url)): ?>
                                    <a href="<?= htmlspecialchars($ejercicio->video_url) ?>" target="_blank" class="btn btn-primary btn-sm" title="Ver Video">
                                        ▶️ Video
                                    </a>
                                <?php endif; ?>
                                <?php if (empty($ejercicio->imagen_url) && empty($ejercicio->video_url)): ?>
                                    <span style="color: var(--color-texto-suave); font-size: 12px;">Sin multimedia</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <a href="PEjercicioEdit.php?id=<?= $ejercicio->id ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <a href="PEjercicioList.php?delete_id=<?= $ejercicio->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar el ejercicio <?= htmlspecialchars($ejercicio->nombre) ?>?');">
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
