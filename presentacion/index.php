<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — PANEL PRINCIPAL (index.php)
// ==========================================================================
// Responsabilidad:
// 1. Mostrar el Dashboard principal del entrenador autenticado.
// 2. Ofrecer accesos rápidos a todos los módulos del sistema.
// 3. Mostrar una tabla resumida con los planes activos más recientes.
// ==========================================================================

$tituloPagina = "Dashboard Principal";
require_once __DIR__ . '/layout/header.php';

require_once __DIR__ . '/../Negocio/NPlanEntrenamiento.php';
$nPlan = new NPlanEntrenamiento();
$entrenadorId = (int)$_SESSION['entrenador_id'];

// Obtener todos los planes del entrenador
$todosLosPlanes = $nPlan->getAll($entrenadorId);
$hoy = date('Y-m-d');

// Filtrar para mostrar solo los planes activos recientes (máximo 5)
$planesRecientes = [];
foreach ($todosLosPlanes as $plan) {
    if ($hoy >= $plan->fecha_inicio && $hoy <= $plan->fecha_fin) {
        $planesRecientes[] = $plan;
        if (count($planesRecientes) >= 5) break;
    }
}
?>

<div class="header-section" style="margin-top: 10px; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 26px;">Bienvenido, <?= htmlspecialchars($nombreEntrenador) ?> </h2>
        <p style="color: var(--color-texto-suave); font-size: 14px; margin-top: 6px;">
            Selecciona un módulo para gestionar tu centro de entrenamiento:
        </p>
    </div>
</div>

<!-- Módulos de Navegación Rápida -->
<div class="dashboard-grid-custom">

    <a href="/PCliente/PClienteList.php" class="dashboard-card-large">
        <div class="icono">👥</div>
        <h3>Clientes</h3>
        <p>Registrar, listar y editar información de clientes</p>
        <span class="abrir-modulo">Abrir Módulo →</span>
    </a>

    <a href="/PEjercicio/PEjercicioList.php" class="dashboard-card-large">
        <div class="icono">💪</div>
        <h3>Ejercicios</h3>
        <p>Catálogo de ejercicios y videos demostrativos</p>
        <span class="abrir-modulo">Abrir Módulo →</span>
    </a>

    <a href="/PRutina/PRutinaList.php" class="dashboard-card-large">
        <div class="icono">📋</div>
        <h3>Rutinas</h3>
        <p>Diseño de rutinas y ejercicios por día</p>
        <span class="abrir-modulo">Abrir Módulo →</span>
    </a>

</div> <!-- end dashboard-grid-custom -->

<div class="bottom-card-container">
    <a href="/PDieta/PDietaList.php" class="dashboard-card-large">
        <div class="icono">🥗</div>
        <h3>Dietas</h3>
        <p>Planes nutricionales, calorías y objetivos</p>
        <span class="abrir-modulo">Abrir Módulo →</span>
    </a>

    <a href="/PPlanEntrenamiento/PPlanEntrenamientoList.php" class="dashboard-card-large">
        <div class="icono">🎯</div>
        <h3>Planes Asignados</h3>
        <p>Asignación de rutinas y dietas a cada cliente</p>
        <span class="abrir-modulo">Abrir Módulo →</span>
    </a>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
