<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — PORTAL DEL CLIENTE (PClientePortal.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar al cliente los planes de entrenamiento asignados a él.
// 2. Invocar a la Capa de Negocio (NPlanEntrenamiento) para obtener datos.
// ==========================================================================

$tituloPagina = "Mis Planes de Entrenamiento";
require_once __DIR__ . '/../layout/header_cliente.php';

require_once __DIR__ . '/../../Negocio/NPlanEntrenamiento.php';

// Paso 1: Invocar a la Capa de Negocio
$nPlan = new NPlanEntrenamiento();
$cliente_id = $_SESSION['cliente_id'];
$listaPlanes = $nPlan->getByClienteId($cliente_id);

?>

<div class="header-action">
    <h2>🎯 Mis Planes de Entrenamiento</h2>
</div>

<?php if (empty($listaPlanes)): ?>
    <div class="alert alert-info">
        No tienes planes de entrenamiento asignados actualmente.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Rutina</th>
                    <th>Tipo Rutina</th>
                    <th>Dieta</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaPlanes as $plan): ?>
                    <?php
                        $hoy = strtotime(date('Y-m-d'));
                        $fin = strtotime($plan->fecha_fin);
                        
                        if ($fin < $hoy) {
                            $estado = "Vencido";
                            $claseEstado = "badge-danger";
                        } else {
                            $estado = "Vigente";
                            $claseEstado = "badge-success";
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($plan->id) ?></td>
                        <td><?= htmlspecialchars($plan->rutina_nombre) ?></td>
                        <td><?= htmlspecialchars($plan->rutina_tipo) ?></td>
                        <td><?= htmlspecialchars($plan->dieta_titulo ?: 'Sin Dieta') ?></td>
                        <td><?= htmlspecialchars($plan->fecha_inicio) ?></td>
                        <td><?= htmlspecialchars($plan->fecha_fin) ?></td>
                        <td><span class="badge <?= $claseEstado ?>"><?= $estado ?></span></td>
                        <td>
                            <a href="PClientePlanDetalle.php?id=<?= $plan->id ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Ver Detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
