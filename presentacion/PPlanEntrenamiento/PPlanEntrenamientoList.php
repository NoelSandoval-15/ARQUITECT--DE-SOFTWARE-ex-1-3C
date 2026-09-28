<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — LISTA DE PLANES (PPlanEntrenamientoList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar los planes asignados a los clientes por el entrenador.
// 2. Calcular visualmente el estado del plan (Activo, Pendiente o Finalizado).
// 3. Comunicarse exclusivamente con NPlanEntrenamiento.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NPlanEntrenamiento.php';

$negocioPlan  = new NPlanEntrenamiento();
$mensajeAlerta = '';
$tipoAlerta = 'info';

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Planes de Entrenamiento";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];

// Paso 1: Procesar eliminación por GET
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultado = $negocioPlan->delete($idEliminar);

    if ($resultado === true) {
        $mensajeAlerta = 'Plan de entrenamiento cancelado/eliminado con éxito.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultado;
        $tipoAlerta = 'danger';
    }
}

// Paso 2: Obtener planes asignados por el entrenador
$listaPlanes = $negocioPlan->getAll($entrenadorId);
$hoy = date('Y-m-d');
?>

<div class="header-section">
    <div>
        <h2>🎯 Planes de Entrenamiento Asignados</h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">Distribución y asignación de rutinas y dietas para cada cliente</p>
    </div>
    <a href="PPlanEntrenamientoCreate.php" class="btn btn-success">+ Asignar Nuevo Plan</a>
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
                <th>Cliente Asignado</th>
                <th>Rutina</th>
                <th>Dieta Nutricional</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Compartir</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaPlanes)): ?>
                <tr>
                    <td colspan="9" class="sin-datos">
                        No hay planes de entrenamiento asignados aún. ¡Crea el primero con "+ Asignar Nuevo Plan"!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($listaPlanes as $plan): ?>
                    <?php
                    // Cálculo de estado temporal del plan
                    if ($hoy < $plan->fecha_inicio) {
                        $badgeClase = 'badge-warning';
                        $estadoTexto = '⏳ Pendiente';
                    } elseif ($hoy > $plan->fecha_fin) {
                        $badgeClase = 'badge-secondary';
                        $estadoTexto = '🏁 Finalizado';
                    } else {
                        $badgeClase = 'badge-success';
                        $estadoTexto = '🟢 Activo';
                    }

                    // Dominio dinámico (localhost o el dominio real cuando se despliegue)
                    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
                    $dominio = $_SERVER['HTTP_HOST'];
                    $linkPortal = $protocolo . $dominio . "/Login.php";

                    // Mensaje para compartir
                    $nombreCliente = htmlspecialchars($plan->cliente_nombre);
                    $rutina = htmlspecialchars($plan->rutina_nombre);
                    $mensajeTexto = "¡Hola $nombreCliente! Te he asignado un nuevo plan de entrenamiento. Rutina: $rutina.\n\nIngresa al portal para ver tus ejercicios: $linkPortal";
                    $mensajeURL = urlencode($mensajeTexto);
                    
                    // Número de teléfono limpio (solo números)
                    $telefonoLimpio = preg_replace('/[^0-9]/', '', $plan->telefono ?? '');
                    
                    $waLink = "https://wa.me/{$telefonoLimpio}?text={$mensajeURL}";
                    $mailLink = "mailto:" . urlencode($plan->correo ?? '') . "?subject=" . urlencode("Nuevo Plan de Entrenamiento Asignado") . "&body={$mensajeURL}";
                    ?>
                    <tr>
                        <td><?= $plan->id ?></td>
                        <td>
                            <strong><?= $nombreCliente ?></strong>
                        </td>
                        <td>
                            <span>📋 <?= htmlspecialchars($plan->rutina_nombre) ?></span>
                            <br>
                            <small style="color: var(--color-texto-suave);">(<?= htmlspecialchars($plan->rutina_tipo) ?>)</small>
                        </td>
                        <td>
                            <span>🥗 <?= htmlspecialchars($plan->dieta_nombre ?: 'Sin Dieta') ?></span>
                        </td>
                        <td><?= htmlspecialchars($plan->fecha_inicio) ?></td>
                        <td><?= htmlspecialchars($plan->fecha_fin) ?></td>
                        <td>
                            <span class="badge <?= $badgeClase ?>"><?= $estadoTexto ?></span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-direction: column;">
                                <?php if (!empty($telefonoLimpio)): ?>
                                    <a href="<?= $waLink ?>" target="_blank" class="btn btn-success btn-sm" style="background: #25D366; border-color: #25D366;">🟢 WhatsApp</a>
                                <?php endif; ?>
                                <?php if (!empty($plan->correo)): ?>
                                    <a href="<?= $mailLink ?>" target="_blank" class="btn btn-primary btn-sm">📧 Correo</a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <a href="PPlanEntrenamientoList.php?delete_id=<?= $plan->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Seguro que deseas eliminar este plan de entrenamiento?');">
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
