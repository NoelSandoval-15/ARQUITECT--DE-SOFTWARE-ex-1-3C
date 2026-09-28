<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — LISTA DE DIETAS (PDietaList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar las dietas nutricionales del entrenador autenticado.
// 2. Gestionar la eliminación invocando a la Capa de Negocio (NDieta).
// 3. Renderizar la tabla de dietas con diseño limpio.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NDieta.php';

$negocioDieta = new NDieta();
$mensajeAlerta = '';
$tipoAlerta = 'info';

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Gestión de Dietas";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 1: Procesar eliminación por GET
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultado = $negocioDieta->delete($idEliminar);

    if ($resultado === true) {
        $mensajeAlerta = 'Dieta eliminada exitosamente.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultado;
        $tipoAlerta = 'danger';
    }
}

// Paso 2: Obtener las dietas del entrenador activo desde la Capa de Negocio
$listaDietas = $negocioDieta->getAll($entrenadorId);
?>

<div class="header-section">
    <div>
        <h2>🥗 Dietas Nutricionales</h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">Planes alimentarios diseñados para asignar a tus clientes</p>
    </div>
    <a href="PDietaCreate.php" class="btn btn-success">+ Nueva Dieta</a>
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
                <th style="width: 280px;">Título de la Dieta</th>
                <th>Descripción y Recomendaciones Nutricionales</th>
                <th style="width: 170px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaDietas)): ?>
                <tr>
                    <td colspan="4" class="sin-datos">
                        No tienes dietas registradas aún. ¡Haz clic en "+ Nueva Dieta" para crear la primera!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($listaDietas as $dieta): ?>
                    <tr>
                        <td><?= $dieta->id ?></td>
                        <td>
                            <strong><?= htmlspecialchars($dieta->titulo) ?></strong>
                        </td>
                        <td style="color: #475569; font-size: 13px;">
                            <details style="cursor: pointer;">
                                <summary style="font-weight: 500; color: #3b82f6; outline: none; user-select: none;">▶ Ver descripción completa</summary>
                                <div style="margin-top: 8px; padding: 12px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 12.5px; line-height: 1.5; color: #334155;">
                                    <?= nl2br(htmlspecialchars($dieta->descripcion)) ?>
                                </div>
                            </details>
                        </td>
                        <td>
                            <a href="PDietaEdit.php?id=<?= $dieta->id ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <a href="PDietaList.php?delete_id=<?= $dieta->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar la dieta: <?= htmlspecialchars($dieta->titulo) ?>?');">
                               🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
