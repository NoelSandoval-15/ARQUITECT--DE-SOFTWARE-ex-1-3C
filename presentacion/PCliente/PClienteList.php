<?php
// ==========================================================================
// CAPA DE PRESENTACIÓN — LISTA DE CLIENTES (PClienteList.php)
// ==========================================================================
// Responsabilidad Única:
// 1. Recibir solicitudes del usuario (ver lista o eliminar).
// 2. Comunicarse ÚNICAMENTE con la Capa de Negocio (NCliente).
// 3. Renderizar la tabla de clientes sin lógica de base de datos.
// ==========================================================================

require_once __DIR__ . '/../../Negocio/NCliente.php';

// Instancia de la Capa de Negocio
$negocioCliente = new NCliente();

// Mensajes de alerta para el usuario
$mensajeAlerta = '';
$tipoAlerta = 'info';

// Paso 1: Procesar solicitud de eliminación si viene el parámetro 'delete_id' por GET
if (isset($_GET['delete_id'])) {
    $idEliminar = (int)$_GET['delete_id'];
    $resultadoEliminacion = $negocioCliente->delete($idEliminar);

    if ($resultadoEliminacion === true) {
        $mensajeAlerta = 'Cliente eliminado exitosamente.';
        $tipoAlerta = 'success';
    } else {
        $mensajeAlerta = $resultadoEliminacion;
        $tipoAlerta = 'danger';
    }
}

// Paso 2: Obtener la lista de clientes desde la Capa de Negocio
$listaClientes = $negocioCliente->getAll();

// Paso 3: Renderizar cabecera común
$tituloPagina = "Gestión de Clientes";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Encabezado de Sección y Botón de Acción -->
<div class="header-section">
    <div>
        <h2>👥 Clientes Registrados</h2>
        <p style="color: var(--color-texto-suave); font-size: 13px;">Listado de clientes inscritos en el gimnasio</p>
    </div>
    <a href="PClienteCreate.php" class="btn btn-success">+ Registrar Nuevo Cliente</a>
</div>

<!-- Mensaje de Notificación (si aplica) -->
<?php if (!empty($mensajeAlerta)): ?>
    <div class="alert alert-<?= $tipoAlerta ?>">
        <?= htmlspecialchars($mensajeAlerta) ?>
    </div>
<?php endif; ?>

<!-- Tabla de Clientes -->
<div class="tabla-card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Apellidos y Nombres</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Sexo</th>
                <th>Altura</th>
                <th>Peso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaClientes)): ?>
                <tr>
                    <td colspan="8" class="sin-datos">
                        No hay clientes registrados en este momento.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($listaClientes as $cliente): ?>
                    <tr>
                        <td><?= $cliente->id ?></td>
                        <td><strong><?= htmlspecialchars($cliente->apellido . ', ' . $cliente->nombre) ?></strong></td>
                        <td><?= htmlspecialchars($cliente->correo) ?></td>
                        <td><?= htmlspecialchars($cliente->telefono) ?></td>
                        <td>
                            <?php
                            $sexo = $cliente->sexo;
                            $etiquetas = ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'];
                            $textoSexo = $etiquetas[$sexo] ?? $sexo;
                            ?>
                            <span class="badge badge-<?= $sexo ?>"><?= $textoSexo ?></span>
                        </td>
                        <td><?= number_format($cliente->altura, 2) ?> m</td>
                        <td><?= number_format($cliente->peso, 1) ?> kg</td>
                        <td>
                            <a href="PClienteEdit.php?id=<?= $cliente->id ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <a href="PClienteList.php?delete_id=<?= $cliente->id ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Confirma que desea eliminar a <?= htmlspecialchars($cliente->nombre) ?>?');">
                               🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
