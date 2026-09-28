<?php
// ============================================================
// CAPA DE NEGOCIO — NCliente.php
// ============================================================
// Responsabilidad ÚNICA: Aplicar validaciones y reglas de
// negocio del módulo Cliente antes de delegar a la capa Datos.
//
// REGLAS:
//   ✔ Valida todos los campos antes de enviarlos a DCliente.
//   ✔ Hashea el password del cliente antes de guardarlo.
//   ✔ Se comunica HACIA ABAJO solo con DCliente.
//   ✘ No contiene SQL.
//   ✘ No genera HTML.
//
// NOTA FUTURA: Agregar método login() cuando se implemente
//   el portal de acceso para el cliente.
// ============================================================

require_once __DIR__ . '/../Datos/DCliente.php';

class NCliente
{
    // Instancia de la capa de Datos
    private DCliente $dCliente;

    public function __construct()
    {
        $this->dCliente = new DCliente();
    }

    // ----------------------------------------------------------
    // CREATE: Validar y registrar un nuevo cliente.
    //
    // El password se hashea aquí antes de enviarlo a la BD.
    // Esto sigue el principio de que la lógica de seguridad
    // pertenece a la capa de Negocio, no a la de Datos.
    //
    // Retorna: true si se registró, string con error si falló.
    // ----------------------------------------------------------
    public function create(
        string $nombre,
        string $apellido,
        string $correo,
        string $telefono,
        string $sexo,
        string $altura,
        string $peso,
        string $password
    ): bool|string {
        // --- Validaciones de campos obligatorios ---
        if (empty(trim($nombre))) {
            return "El nombre del cliente es obligatorio.";
        }
        if (empty(trim($apellido))) {
            return "El apellido del cliente es obligatorio.";
        }
        if (empty(trim($correo)) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "Se requiere un correo electrónico válido.";
        }
        if (empty(trim($telefono))) {
            return "El teléfono es obligatorio.";
        }
        if (!in_array($sexo, ['M', 'F', 'O'])) {
            return "El sexo debe ser M (Masculino), F (Femenino) u O (Otro).";
        }
        if (!is_numeric($altura) || (float)$altura <= 0) {
            return "La altura debe ser un número positivo (ej: 1.75).";
        }
        if (!is_numeric($peso) || (float)$peso <= 0) {
            return "El peso debe ser un número positivo (ej: 70.5).";
        }
        if (strlen(trim($password)) < 4) {
            return "La contraseña del cliente debe tener al menos 4 caracteres.";
        }

        // --- Hashear password antes de guardar en BD ---
        // La contraseña NUNCA se guarda en texto plano
        $passwordHasheado = password_hash(trim($password), PASSWORD_DEFAULT);

        // --- Delegar inserción a la capa de Datos ---
        $resultado = $this->dCliente->create(
            trim($nombre),
            trim($apellido),
            trim($correo),
            trim($telefono),
            $sexo,
            (float)$altura,
            (float)$peso,
            $passwordHasheado
        );

        return $resultado ? true : "Error al registrar el cliente. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET ALL: Obtener todos los clientes registrados.
    // Retorna: arreglo de objetos con los clientes.
    // ----------------------------------------------------------
    public function getAll(): array
    {
        return $this->dCliente->findAll();
    }

    // ----------------------------------------------------------
    // GET BY ID: Obtener un cliente específico por su ID.
    // Retorna: objeto con datos, o null si no existe.
    // ----------------------------------------------------------
    public function getById(int $id): ?object
    {
        if ($id <= 0) return null;
        return $this->dCliente->findById($id);
    }

    // ----------------------------------------------------------
    // UPDATE: Validar y actualizar datos de un cliente.
    // El password NO se actualiza en esta operación.
    // Retorna: true si actualizó, string con error si falló.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $nombre,
        string $apellido,
        string $correo,
        string $telefono,
        string $sexo,
        string $altura,
        string $peso
    ): bool|string {
        if ($id <= 0) return "ID de cliente no válido.";

        if (empty(trim($nombre))) {
            return "El nombre del cliente es obligatorio.";
        }
        if (empty(trim($apellido))) {
            return "El apellido del cliente es obligatorio.";
        }
        if (empty(trim($correo)) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "Se requiere un correo electrónico válido.";
        }
        if (empty(trim($telefono))) {
            return "El teléfono es obligatorio.";
        }
        if (!in_array($sexo, ['M', 'F', 'O'])) {
            return "El sexo debe ser M, F u O.";
        }
        if (!is_numeric($altura) || (float)$altura <= 0) {
            return "La altura debe ser un número positivo (ej: 1.75).";
        }
        if (!is_numeric($peso) || (float)$peso <= 0) {
            return "El peso debe ser un número positivo (ej: 70.5).";
        }

        $resultado = $this->dCliente->update(
            $id,
            trim($nombre),
            trim($apellido),
            trim($correo),
            trim($telefono),
            $sexo,
            (float)$altura,
            (float)$peso
        );

        return $resultado ? true : "Error al actualizar el cliente. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar un cliente del sistema.
    // Retorna: true si eliminó, string con error si falló.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID de cliente no válido.";

        $resultado = $this->dCliente->delete($id);
        return $resultado ? true : "Error al eliminar el cliente. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // LOGIN: Verificar credenciales del cliente
    // Retorna: objeto con los datos del cliente si tiene éxito, 
    // false si las credenciales son incorrectas, string en caso de error.
    // ----------------------------------------------------------
    public function login(string $correo, string $password): object|bool|string
    {
        if (empty(trim($correo)) || empty(trim($password))) {
            return "El correo y la contraseña son obligatorios.";
        }

        // 1. Obtener al cliente por su correo
        $cliente = $this->dCliente->findByCorreo(trim($correo));
        
        // 2. Si no existe, credenciales inválidas
        if (!$cliente) {
            return false;
        }

        // 3. Verificar el hash del password
        if (password_verify($password, $cliente->password)) {
            // Eliminar el hash de la contraseña antes de devolver el objeto 
            // por seguridad en la capa de presentación
            unset($cliente->password);
            return $cliente;
        }

        return false;
    }
}
