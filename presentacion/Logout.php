<?php
// ============================================================
// CAPA DE PRESENTACIÓN — Logout.php
// ============================================================
// Responsabilidad ÚNICA: Cerrar la sesión activa del entrenador
// y redirigirlo a la página de Login.
//
// FLUJO:
//   1. Inicia la sesión (para poder leerla y destruirla).
//   2. Destruye todos los datos de la sesión.
//   3. Redirige al Login.
//
// Esta página NO muestra HTML, solo realiza la acción y redirige.
// ============================================================

// Iniciamos la sesión para poder acceder a ella y destruirla
session_start();

// Eliminamos todas las variables de la sesión activa
$_SESSION = [];

// Destruimos la sesión del servidor por completo
session_destroy();

// Redirigimos al formulario de Login
header('Location: Login.php');
exit();
