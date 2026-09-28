<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — CREAR CLIENTE (PClienteCreate.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Mostrar el formulario de registro de un nuevo cliente.
// 2. Recibir los datos enviados por el usuario vía POST.
// 3. Enviar los datos a la Capa de Negocio (NCliente) para su validación.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NCliente.php';

$mensajeError = '';

// Paso 1: Si se envió el formulario, procesar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización básica de entradas
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $sexo     = trim($_POST['sexo'] ?? '');
    $altura   = trim($_POST['altura'] ?? '');
    $peso     = trim($_POST['peso'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Paso 2: Invocar la Capa de Negocio
    $negocioCliente = new NCliente();
    $resultado = $negocioCliente->create($nombre, $apellido, $correo, $telefono, $sexo, $altura, $peso, $password);

    // Paso 3: Evaluar la respuesta de Negocio
    if ($resultado === true) {
        header('Location: PClienteList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}

// Paso 4: Renderizar cabecera común
$tituloPagina = "Registrar Cliente";
require_once __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>➕ Registrar Nuevo Cliente</h2>

    <!-- Mostrar error devuelto por la Capa de Negocio si falló la validación -->
    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PClienteCreate.php">

        <div class="form-row-2">
            <div class="form-group">
                <label for="nombre">Nombre(s) *</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                       placeholder="Ej: Carlos" required autofocus>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido(s) *</label>
                <input type="text" id="apellido" name="apellido" 
                       value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" 
                       placeholder="Ej: Pérez Mendoza" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" 
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" 
                       placeholder="carlos@ejemplo.com" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono" 
                       value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" 
                       placeholder="Ej: 71234567" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="sexo">Sexo *</label>
                <select id="sexo" name="sexo" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="M" <?= (($_POST['sexo'] ?? '') === 'M') ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= (($_POST['sexo'] ?? '') === 'F') ? 'selected' : '' ?>>Femenino</option>
                    <option value="O" <?= (($_POST['sexo'] ?? '') === 'O') ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Contraseña de Acceso *</label>
                <input type="password" id="password" name="password" 
                       placeholder="Mínimo 4 caracteres" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="altura">Altura (metros) *</label>
                <input type="number" step="0.01" min="0.5" max="2.5" id="altura" name="altura" 
                       value="<?= htmlspecialchars($_POST['altura'] ?? '') ?>" 
                       placeholder="Ej: 1.75" required>
            </div>
            <div class="form-group">
                <label for="peso">Peso (kilogramos) *</label>
                <input type="number" step="0.1" min="20" max="300" id="peso" name="peso" 
                       value="<?= htmlspecialchars($_POST['peso'] ?? '') ?>" 
                       placeholder="Ej: 72.5" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="PClienteList.php" class="btn btn-secondary">← Cancelar</a>
            <button type="submit" class="btn btn-success" style="flex: 1;">💾 Guardar Cliente</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
