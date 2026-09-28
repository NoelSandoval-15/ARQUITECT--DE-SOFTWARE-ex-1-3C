<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — ASIGNAR PLAN DE ENTRENAMIENTO (PPlanEntrenamientoCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Cargar las opciones desde la Capa de Negocio (Clientes, Rutinas, Dietas).
// 2. Renderizar el formulario de vinculación de plan con rango de fechas.
// 3. Procesar el envío vía POST e invocar NPlanEntrenamiento.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NPlanEntrenamiento.php';
require_once __DIR__ . '/../../Negocio/NCliente.php';
require_once __DIR__ . '/../../Negocio/NRutina.php';
require_once __DIR__ . '/../../Negocio/NDieta.php';

$negocioPlan    = new NPlanEntrenamiento();
$negocioCliente = new NCliente();
$negocioRutina  = new NRutina();
$negocioDieta   = new NDieta();

// Iniciar sesión y validar entrenador mediante el header
$tituloPagina = "Asignar Plan";
require_once __DIR__ . '/../layout/header.php';

$entrenadorId = (int)$_SESSION['entrenador_id'];
$mensajeError = '';

// Paso 1: Cargar datos para los selectores desde la Capa de Negocio
$listaClientes = $negocioCliente->getAll();
$listaRutinas  = $negocioRutina->getAll($entrenadorId);
$listaDietas   = $negocioDieta->getAll($entrenadorId);

// Paso 2: Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id   = (int)($_POST['cliente_id'] ?? 0);
    $rutina_id    = (int)($_POST['rutina_id'] ?? 0);
    $dieta_id     = (int)($_POST['dieta_id'] ?? 0);
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin    = trim($_POST['fecha_fin'] ?? '');

    $resultado = $negocioPlan->create($cliente_id, $rutina_id, $dieta_id, $fecha_inicio, $fecha_fin);

    if ($resultado === true) {
        header('Location: PPlanEntrenamientoList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}
?>

<div class="form-card" style="max-width: 620px;">
    <h2>🎯 Asignar Plan de Entrenamiento</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <!-- Aviso pedagógico si faltan requisitos -->
    <?php if (empty($listaClientes) || empty($listaRutinas)): ?>
        <div class="alert alert-info" style="font-size: 13px;">
            ℹ️ <strong>Requisitos previos:</strong> Para crear un plan necesitas tener registrado al menos un cliente y una rutina. (La dieta es opcional).
        </div>
    <?php endif; ?>

    <form method="POST" action="PPlanEntrenamientoCreate.php">

        <div class="form-group">
            <label for="cliente_id">1. Cliente Beneficiario *</label>
            <select id="cliente_id" name="cliente_id" required autofocus>
                <option value="">-- Seleccionar Cliente --</option>
                <?php foreach ($listaClientes as $c): ?>
                    <option value="<?= $c->id ?>" <?= (($_POST['cliente_id'] ?? '') == $c->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c->apellido . ', ' . $c->nombre) ?> (<?= htmlspecialchars($c->correo) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="rutina_id">2. Rutina de Entrenamiento *</label>
            <select id="rutina_id" name="rutina_id" required>
                <option value="">-- Seleccionar Rutina --</option>
                <?php foreach ($listaRutinas as $r): ?>
                    <option value="<?= $r->id ?>" <?= (($_POST['rutina_id'] ?? '') == $r->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r->nombre) ?> [<?= htmlspecialchars($r->tipo) ?>]
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="dieta_id">3. Dieta / Plan Nutricional (Opcional)</label>
            <select id="dieta_id" name="dieta_id">
                <option value="0">-- Sin Dieta Asignada --</option>
                <?php foreach ($listaDietas as $d): ?>
                    <option value="<?= $d->id ?>" <?= (($_POST['dieta_id'] ?? '') == $d->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d->titulo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio *</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha de Culminación *</label>
                <input type="date" id="fecha_fin" name="fecha_fin" 
                       value="<?= htmlspecialchars($_POST['fecha_fin'] ?? date('Y-m-d', strtotime('+30 days'))) ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="PPlanEntrenamientoList.php" class="btn btn-secondary">← Volver al Listado</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Asignar Plan al Cliente</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
