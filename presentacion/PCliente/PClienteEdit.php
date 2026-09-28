<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — EDITAR CLIENTE (PClienteEdit.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Cargar la información del cliente mediante la Capa de Negocio (NCliente).
// 2. Renderizar el formulario con los valores actuales.
// 3. Procesar la actualización vía POST enviando los datos a NCliente.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NCliente.php';

$negocioCliente = new NCliente();
$mensajeError   = '';

// Paso 1: Obtener el identificador del cliente desde GET
$idCliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cliente   = $negocioCliente->getById($idCliente);

// Si el cliente no existe en la BD, regresar al listado
if (!$cliente) {
    header('Location: PClienteList.php');
    exit();
}

// Paso 2: Procesar la actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $sexo     = trim($_POST['sexo'] ?? '');
    $altura   = trim($_POST['altura'] ?? '');
    $peso     = trim($_POST['peso'] ?? '');

    // Invocar Capa de Negocio para validar y actualizar
    $resultado = $negocioCliente->update($idCliente, $nombre, $apellido, $correo, $telefono, $sexo, $altura, $peso);

    if ($resultado === true) {
        header('Location: PClienteList.php');
        exit();
    } else {
        $mensajeError = $resultado;
    }
}

// Paso 3: Renderizar cabecera común
$tituloPagina = "Editar Cliente #" . $idCliente;
require_once __DIR__ . '/../layout/header.php';
?>

<div class="form-card">
    <h2>✏️ Editar Información de Cliente</h2>

    <!-- Mostrar error devuelto por la Capa de Negocio si falló la validación -->
    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="PClienteEdit.php?id=<?= $idCliente ?>">

        <div class="form-row-2">
            <div class="form-group">
                <label for="nombre">Nombre(s) *</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($_POST['nombre'] ?? $cliente->nombre) ?>" 
                       required autofocus>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido(s) *</label>
                <input type="text" id="apellido" name="apellido" 
                       value="<?= htmlspecialchars($_POST['apellido'] ?? $cliente->apellido) ?>" 
                       required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" 
                       value="<?= htmlspecialchars($_POST['correo'] ?? $cliente->correo) ?>" 
                       required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono" 
                       value="<?= htmlspecialchars($_POST['telefono'] ?? $cliente->telefono) ?>" 
                       required>
            </div>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo *</label>
            <?php $sexoActual = $_POST['sexo'] ?? $cliente->sexo; ?>
            <select id="sexo" name="sexo" required>
                <option value="M" <?= ($sexoActual === 'M') ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= ($sexoActual === 'F') ? 'selected' : '' ?>>Femenino</option>
                <option value="O" <?= ($sexoActual === 'O') ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="altura">Altura (metros) *</label>
                <input type="number" step="0.01" min="0.5" max="2.5" id="altura" name="altura" 
                       value="<?= htmlspecialchars($_POST['altura'] ?? $cliente->altura) ?>" 
                       required>
            </div>
            <div class="form-group">
                <label for="peso">Peso (kilogramos) *</label>
                <input type="number" step="0.1" min="20" max="300" id="peso" name="peso" 
                       value="<?= htmlspecialchars($_POST['peso'] ?? $cliente->peso) ?>" 
                       required>
            </div>
        </div>

        <div class="form-actions">
            <a href="PClienteList.php" class="btn btn-secondary">← Cancelar</a>
            <button type="submit" class="btn btn-warning" style="flex: 1;">💾 Actualizar Cambios</button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
