<?php
// ==========================================================================
// PLANTILLA COMÚN — CABECERA Y NAVEGACIÓN CLIENTE (header_cliente.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Controlar que el usuario tenga sesión activa de Cliente.
// 2. Cargar la hoja de estilos global (/css/estilos.css).
// 3. Renderizar el menú de navegación superior y saludo al cliente.
// ==========================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de Acceso: Redirigir a Login si no hay sesión activa de cliente
if (!isset($_SESSION['cliente_id'])) {
    header('Location: /Login.php');
    exit();
}

// Variables de contexto
$nombreCliente = $_SESSION['cliente_nombre'] ?? 'Cliente';
$titulo = isset($tituloPagina) ? $tituloPagina . ' — Portal Cliente' : 'Portal Cliente — Personal Training';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <!-- Hoja de estilos centralizada -->
    <link rel="stylesheet" href="/css/estilos.css?v=<?= time() ?>">
    <style>
        /* Restaurar la visibilidad de la navbar exclusiva para el cliente 
           (se ocultó globalmente al crear el Sidebar del entrenador) */
        .navbar {
            display: flex !important;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="navbar-brand">
        <a href="/PCliente/PClientePortal.php">🏋️ <strong>Personal Training (Cliente)</strong></a>
    </div>

    <!-- Menú de navegación cliente -->
    <nav class="navbar-links">
        <a href="/PCliente/PClientePortal.php" class="active">Mis Planes</a>
    </nav>

    <!-- Información de sesión y salida -->
    <div class="navbar-user">
        <span>👤 <?= htmlspecialchars($nombreCliente) ?></span>
        <a href="/Logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</header>

<!-- Contenedor principal de contenido para cada vista -->
<main class="container">
