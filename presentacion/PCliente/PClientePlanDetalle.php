<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — DETALLE DEL PLAN (PClientePlanDetalle.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar al cliente todos los detalles de su plan de entrenamiento
//    incluyendo los ejercicios con imágenes, videos y dieta.
// ==========================================================================

$tituloPagina = "Detalle del Plan";
require_once __DIR__ . '/../layout/header_cliente.php';

require_once __DIR__ . '/../../Negocio/NPlanEntrenamiento.php';
require_once __DIR__ . '/../../Negocio/NRutinaEjercicio.php';
require_once __DIR__ . '/../../Negocio/NDieta.php';

$plan_id = (int)($_GET['id'] ?? 0);

if ($plan_id <= 0) {
    echo "<div class='alert alert-danger'>ID de plan no válido.</div>";
    require_once __DIR__ . '/../layout/footer.php';
    exit();
}

// 1. Obtener el Plan
$nPlan = new NPlanEntrenamiento();
$plan = $nPlan->getById($plan_id);

if (!$plan || $plan->cliente_id !== $_SESSION['cliente_id']) {
    echo "<div class='alert alert-danger'>No tienes acceso a este plan de entrenamiento.</div>";
    require_once __DIR__ . '/../layout/footer.php';
    exit();
}

// 2. Obtener los Ejercicios de la Rutina
$nRutinaEjercicio = new NRutinaEjercicio();
$ejercicios = $nRutinaEjercicio->getByRutina($plan->rutina_id);

// 3. Obtener la Dieta (si existe)
$nDieta = new NDieta();
$dieta = null;
if (!empty($plan->dieta_id)) {
    $dieta = $nDieta->getByIdForClient((int)$plan->dieta_id);
}

?>

<div class="header-action">
    <h2>🎯 Plan de Entrenamiento #<?= $plan->id ?></h2>
    <a href="PClientePortal.php" class="btn btn-secondary">← Volver a Mis Planes</a>
</div>

<div class="dashboard-grid" style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 25px; margin-top: 20px; align-items: start;">
    
    <!-- Sección de Dieta -->
    <div class="form-card" style="margin: 0; max-width: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); min-width: 0;">
        <h3 style="margin-bottom: 15px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">🥗 Dieta Asignada: <?= htmlspecialchars($dieta ? $dieta->titulo : 'Sin Dieta') ?></h3>
        <?php if ($dieta): ?>
            <p style="white-space: pre-line; color: #475569; line-height: 1.6; font-size: 15px;">
                <?= htmlspecialchars($dieta->descripcion) ?>
            </p>
        <?php else: ?>
            <p>No hay información de dieta disponible.</p>
        <?php endif; ?>
        <p style="margin-top: 15px; font-size: 13px; color: #64748b;">
            Vigencia: del <strong><?= htmlspecialchars($plan->fecha_inicio) ?></strong> al <strong><?= htmlspecialchars($plan->fecha_fin) ?></strong>
        </p>
    </div>

    <!-- Sección de Rutina y Ejercicios -->
    <div class="form-card" style="margin: 0; max-width: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); min-width: 0; overflow-x: auto;">
        <h3 style="margin-bottom: 15px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">📋 Rutina: <?= htmlspecialchars($plan->rutina_nombre) ?></h3>
        
        <?php if (empty($ejercicios)): ?>
            <div class="alert alert-info">Esta rutina aún no tiene ejercicios asignados.</div>
        <?php else: ?>
            
            <?php 
            // Agrupar ejercicios por día
            $ejerciciosPorDia = [];
            foreach ($ejercicios as $ej) {
                $ejerciciosPorDia[$ej->dia_rutina][] = $ej;
            }
            ?>

            <?php foreach ($ejerciciosPorDia as $dia => $listaEjercicios): ?>
                <h4 style="margin-top: 20px; color: #3b82f6; background: #f8fafc; padding: 10px; border-radius: 6px;">Día: <?= htmlspecialchars($dia) ?></h4>
                <div class="table-responsive" style="width: 100%; overflow-x: auto;">
                    <table class="table" style="width: 100%; min-width: 500px; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Ejercicio</th>
                                <th>Series</th>
                                <th>Reps</th>
                                <th>Duración</th>
                                <th>Descanso</th>
                                <th>Visuales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listaEjercicios as $ej): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?= htmlspecialchars($ej->ejercicio_nombre) ?></td>
                                    <td><?= htmlspecialchars($ej->series) ?></td>
                                    <td><?= htmlspecialchars($ej->repeticiones) ?></td>
                                    <td><?= htmlspecialchars($ej->duracion) ?> seg</td>
                                    <td><?= htmlspecialchars($ej->descanso) ?> seg</td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <?php if (!empty($ej->imagen_url)): ?>
                                                <a href="<?= htmlspecialchars($ej->imagen_url) ?>" target="_blank" class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;">📷 Imagen</a>
                                            <?php endif; ?>
                                            <?php if (!empty($ej->video_url)): ?>
                                                <a href="<?= htmlspecialchars($ej->video_url) ?>" target="_blank" class="btn btn-primary" style="padding: 4px 8px; font-size: 11px; background: #ef4444; border-color: #ef4444;">📺 Video</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
