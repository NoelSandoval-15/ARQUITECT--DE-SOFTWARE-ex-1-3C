<?php
// ==========================================================================
// PLANTILLA COMÚN — CABECERA Y NAVEGACIÓN (header.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Controlar que el usuario tenga sesión activa de Entrenador.
// 2. Cargar la hoja de estilos global (/css/estilos.css).
// 3. Renderizar el menú de navegación superior y saludo al usuario.
// ==========================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de Acceso: Redirigir a Login si no hay sesión activa
if (!isset($_SESSION['entrenador_id'])) {
    header('Location: /Login.php');
    exit();
}

// Variables de contexto
$nombreEntrenador = $_SESSION['entrenador_nombre'] ?? 'Entrenador';
$titulo = isset($tituloPagina) ? $tituloPagina . ' — Personal Training' : 'Personal Training';

// Detección de módulo activo para resaltar el enlace en el menú
$rutaActual = $_SERVER['PHP_SELF'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <!-- Hoja de estilos centralizada con cache-buster -->
    <link rel="stylesheet" href="/css/estilos.css?v=<?= time() ?>">
    <style>
        /* Ajustes específicos para igualar el mockup exactamente */
        body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .app-layout { display: flex; min-height: 100vh; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 250px; background-color: #2a3042; color: #a6b0cf; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 20px; font-size: 16px; font-weight: bold; color: #ffffff; display: flex; align-items: center; gap: 10px; background-color: #2a3042; }
        .sidebar-brand img { width: 24px; } /* si hubiera logo */
        .sidebar-section-title { padding: 20px 20px 10px; font-size: 11px; font-weight: 600; color: #6b7790; text-transform: uppercase; letter-spacing: 0.5px; }
        .sidebar-links { display: flex; flex-direction: column; padding: 0 15px; gap: 4px; }
        .sidebar-links a { display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; color: #a6b0cf; text-decoration: none; font-size: 13.5px; border-radius: 4px; transition: all 0.3s; }
        .sidebar-links a:hover { color: #ffffff; }
        .sidebar-links a.active { color: #ffffff; border: 1px solid #ffffff; background-color: rgba(255, 255, 255, 0.05); }
        .sidebar-links a i { margin-right: 10px; font-size: 16px; width: 20px; text-align: center; }
        
        /* BADGES EN SIDEBAR */
        .badge-count { font-size: 12px; color: #a6b0cf; }
        .badge-count.green { background-color: #34c38f; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        
        /* TOPBAR */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; background-color: #f4f7f6; overflow-y: auto; overflow-x: hidden; }
        .topbar { background-color: #2a3042; height: 70px; display: flex; justify-content: flex-end; align-items: center; padding: 0 25px; }
        .navbar-user { display: flex; align-items: center; gap: 15px; color: #a6b0cf; font-size: 14px; }
        .btn-logout { background-color: #f46a6a; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 500; }
        
        /* DASHBOARD CONTENT */
        .main-content { padding: 30px; }
        .header-section h2 { font-size: 22px; color: #495057; margin-bottom: 5px; font-weight: 600; }
        .header-section p { color: #74788d; font-size: 14px; margin-top: 0; }
        
        /* CARDS GRID */
        .dashboard-grid-custom { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .dashboard-card-large { background: #ffffff; border-radius: 4px; padding: 30px 20px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 12px; border: 1px solid #f1f5f7; transition: all 0.2s; }
        .dashboard-card-large:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .dashboard-card-large .icono { font-size: 32px; }
        .dashboard-card-large h3 { color: #495057; font-size: 16px; margin: 0; font-weight: 600; }
        .dashboard-card-large p { color: #74788d; font-size: 13px; margin: 0; line-height: 1.5; }
        .abrir-modulo { color: #556ee6; font-size: 13px; font-weight: 500; margin-top: auto; }
        
        /* BOTTOM CARDS CENTERING */
        .dashboard-card-large.bottom-card { width: 100%; max-width: 100%; }
        .bottom-card-container { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .bottom-card-container > a { width: calc(33.333% - 13.33px); }
    </style>
</head>
<body>
<div class="app-layout">
    <!-- Panel Lateral (Sidebar) -->
    <aside class="sidebar">
        <div class="sidebar-brand">
             🏆 Personal Training
        </div>
        
        <div class="sidebar-section-title">MÓDULOS / TABLAS BD</div>
        
        <nav class="sidebar-links">
            <?php 
                // Función auxiliar para determinar si una ruta está activa
                function isActive($rutas) {
                    global $rutaActual;
                    foreach((array)$rutas as $r) {
                        if (strpos($rutaActual, $r) !== false) return 'active';
                    }
                    return '';
                }
                
                require_once __DIR__ . '/../../Negocio/NCliente.php';
                require_once __DIR__ . '/../../Negocio/NEjercicio.php';
                require_once __DIR__ . '/../../Negocio/NRutina.php';
                require_once __DIR__ . '/../../Negocio/NDieta.php';
                require_once __DIR__ . '/../../Negocio/NPlanEntrenamiento.php';
                
                $nC = new NCliente();
                $nE = new NEjercicio();
                $nR = new NRutina();
                $nD = new NDieta();
                $nP = new NPlanEntrenamiento();
                
                $countClientes = count($nC->getAll());
                $countEjercicios = count($nE->getAll());
                $countRutinas = count($nR->getAll($_SESSION['entrenador_id'] ?? 0));
                $countDietas = count($nD->getAll($_SESSION['entrenador_id'] ?? 0));
                $countPlanes = count($nP->getAll($_SESSION['entrenador_id'] ?? 0));
            ?>
            <a href="/index.php" class="<?= $rutaActual == '/index.php' ? 'active' : '' ?>">
                <div><i>📊</i> Dashboard Principal</div>
            </a>
            
            <a href="/PCliente/PClienteList.php" class="<?= isActive('PCliente') ?>">
                <div><i>👥</i> Clientes</div>
                <span class="badge-count"><?= $countClientes ?></span>
            </a>
            
            <a href="/PEjercicio/PEjercicioList.php" class="<?= isActive('PEjercicio') ?>">
                <div><i>💪</i> Ejercicios</div>
                <span class="badge-count"><?= $countEjercicios ?></span>
            </a>
            
            <a href="/PRutina/PRutinaList.php" class="<?= isActive('PRutina') ?>">
                <div><i>📋</i> Rutinas</div>
                <span class="badge-count"><?= $countRutinas ?></span>
            </a>
            
            <a href="/PDieta/PDietaList.php" class="<?= isActive('PDieta') ?>">
                <div><i>🥗</i> Dietas Nutricionales</div>
                <span class="badge-count"><?= $countDietas ?></span>
            </a>
            
            <a href="/PPlanEntrenamiento/PPlanEntrenamientoList.php" class="<?= isActive('PPlanEntrenamiento') ?>">
                <div><i>🎯</i> Planes Asignados</div>
                <span class="badge-count green"><?= $countPlanes ?></span>
            </a>
        </nav>
    </aside>

    <!-- Contenedor Principal Derecho -->
    <div class="main-wrapper">
        <!-- Barra Superior (Topbar) -->
        <header class="topbar">
            <div class="navbar-user">
                <span>👤 <?= htmlspecialchars($nombreEntrenador) ?></span>
                <a href="/Logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Central -->
        <main class="main-content">
