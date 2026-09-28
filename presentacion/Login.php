<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — INICIO DE SESIÓN (Login.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar formulario de autenticación.
// 2. Recibir credenciales vía POST.
// 3. Delegar la verificación a la Capa de Negocio correspondiente.
// ==========================================================================

session_start();

// Si ya tiene sesión activa, redirigir al Dashboard correspondiente
if (isset($_SESSION['entrenador_id'])) {
    header('Location: index.php');
    exit();
} elseif (isset($_SESSION['cliente_id'])) {
    header('Location: PCliente/PClientePortal.php');
    exit();
}

require_once __DIR__ . '/../Negocio/NEntrenador.php';
require_once __DIR__ . '/../Negocio/NCliente.php';

$mensajeError = '';

// Paso 1: Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol      = trim($_POST['rol'] ?? 'entrenador');

    if ($rol === 'entrenador') {
        $negocioEntrenador = new NEntrenador();
        $resultado = $negocioEntrenador->login($correo, $password);

        if (is_object($resultado)) {
            // Guardar datos en sesión (Responsabilidad de la Presentación)
            $_SESSION['entrenador_id']     = $resultado->id;
            $_SESSION['entrenador_nombre'] = $resultado->nombre;
            $_SESSION['entrenador_correo'] = $resultado->correo;

            header('Location: index.php');
            exit();
        } else {
            $mensajeError = is_string($resultado) ? $resultado : "Credenciales incorrectas.";
        }
    } elseif ($rol === 'cliente') {
        $negocioCliente = new NCliente();
        $resultado = $negocioCliente->login($correo, $password);

        if (is_object($resultado)) {
            // Guardar datos en sesión
            $_SESSION['cliente_id']     = $resultado->id;
            $_SESSION['cliente_nombre'] = $resultado->nombre . ' ' . $resultado->apellido;
            
            header('Location: PCliente/PClientePortal.php');
            exit();
        } elseif ($resultado === false) {
            $mensajeError = "Credenciales incorrectas.";
        } else {
            $mensajeError = $resultado;
        }
    } else {
        $mensajeError = "Rol no válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Personal Training</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .login-box {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
        }
        .login-box h1 {
            font-size: 24px;
            color: #1e293b;
            text-align: center;
            margin-bottom: 6px;
        }
        .login-box p.sub {
            text-align: center;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }
        .role-switch {
            display: flex;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .role-switch label {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
            transition: 0.3s;
            margin: 0;
        }
        .role-switch input[type="radio"] {
            display: none;
        }
        .role-switch input[type="radio"]:checked + label {
            background: #3b82f6;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1> ENTRENADOR PERSONAL</h1>
    <p class="sub"></p>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="Login.php">

        <div class="role-switch">
            <input type="radio" id="rol_entrenador" name="rol" value="entrenador" <?= (($_POST['rol'] ?? 'entrenador') === 'entrenador') ? 'checked' : '' ?>>
            <label for="rol_entrenador">Entrenador</label>

            <input type="radio" id="rol_cliente" name="rol" value="cliente" <?= (($_POST['rol'] ?? '') === 'cliente') ? 'checked' : '' ?>>
            <label for="rol_cliente">Cliente</label>
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico</label>
            <input type="email" id="correo" name="correo" 
                   value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" 
                   placeholder="ejemplo@correo.com" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" 
                   placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px; padding: 12px;">
            🚀 Ingresar al Sistema
        </button>

    </form>
</div>

</body>
</html>
