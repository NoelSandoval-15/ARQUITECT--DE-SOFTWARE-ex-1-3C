<?php
// ============================================================
// CAPA DE NEGOCIO — NEntrenador.php
// ============================================================
// Responsabilidad ÚNICA: Aplicar las reglas y validaciones del
// negocio relacionadas con el Entrenador.
//
// REGLAS que esta clase SIEMPRE debe respetar:
//   ✔ Valida que los datos recibidos sean correctos antes de
//     pasarlos a la capa de Datos.
//   ✔ Gestiona la sesión del entrenador autenticado (login/logout).
//   ✔ Se comunica HACIA ABAJO con DEntrenador (capa de Datos).
//   ✔ Devuelve resultados claros (true/false o arreglos) a la Presentación.
//   ✘ No contiene sentencias SQL.
//   ✘ No genera HTML ni accede a la interfaz de usuario.
// ============================================================

require_once __DIR__ . '/../Datos/DEntrenador.php';

class NEntrenador
{
    // Instancia de la capa de Datos que esta clase utilizará
    private DEntrenador $dEntrenador;

    // El constructor inicializa la dependencia con la capa de Datos
    public function __construct()
    {
        $this->dEntrenador = new DEntrenador();
    }

    // ----------------------------------------------------------
    // LOGIN — Autenticar al entrenador en el sistema.
    //
    // Proceso:
    //   1. Valida que correo y password no estén vacíos.
    //   2. Busca al entrenador por correo en la BD (via DEntrenador).
    //   3. Verifica la contraseña usando password_verify().
    //   4. Si todo es correcto, guarda la sesión y retorna true.
    //
    // Retorna: true si autenticó exitosamente, string con el mensaje
    //          de error si alguna validación falló.
    // ----------------------------------------------------------
    public function login(string $correo, string $password): object|string
    {
        // --- Validación básica de campos vacíos ---
        if (empty(trim($correo))) {
            return "El correo es obligatorio.";
        }
        if (empty(trim($password))) {
            return "La contraseña es obligatoria.";
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo no es válido.";
        }

        // --- Buscar entrenador en la base de datos ---
        $entrenador = $this->dEntrenador->findByCorreo($correo);

        // Si no existe ningún entrenador con ese correo
        if ($entrenador === null) {
            return "Correo o contraseña incorrectos.";
        }

        // --- Verificar la contraseña contra el hash guardado en BD ---
        // password_verify() compara la contraseña ingresada con el hash
        if (!password_verify($password, $entrenador->password)) {
            return "Correo o contraseña incorrectos.";
        }

        // --- Autenticación exitosa: retornar el objeto ---
        // La Capa de Presentación se encargará de guardar la sesión.
        return $entrenador;
    }

    // ----------------------------------------------------------
    // REGISTRAR — Crear un nuevo entrenador en el sistema.
    //
    // Proceso:
    //   1. Valida que todos los campos sean correctos.
    //   2. Hashea la contraseña de forma segura (password_hash).
    //   3. Delega la inserción a DEntrenador->create().
    //
    // Retorna: true si se registró, string con mensaje de error si falló.
    // ----------------------------------------------------------
    public function registrar(
        string $nombre,
        string $correo,
        string $password,
        string $especialidad,
        string $telefono
    ): bool|string {
        // --- Validaciones de negocio ---
        if (empty(trim($nombre))) {
            return "El nombre es obligatorio.";
        }
        if (empty(trim($correo)) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "Se requiere un correo electrónico válido.";
        }
        if (strlen($password) < 4) {
            return "La contraseña debe tener al menos 4 caracteres.";
        }
        if (empty(trim($especialidad))) {
            return "La especialidad es obligatoria.";
        }
        if (empty(trim($telefono))) {
            return "El teléfono es obligatorio.";
        }

        // --- Verificar que el correo no esté ya registrado ---
        $existe = $this->dEntrenador->findByCorreo($correo);
        if ($existe !== null) {
            return "Ya existe un entrenador registrado con ese correo.";
        }

        // --- Hashear la contraseña antes de guardarla ---
        // NUNCA se guarda la contraseña en texto plano en la base de datos
        $passwordHasheado = password_hash($password, PASSWORD_DEFAULT);

        // --- Delegar la inserción a la capa de Datos ---
        $resultado = $this->dEntrenador->create(
            trim($nombre),
            trim($correo),
            $passwordHasheado,
            trim($especialidad),
            trim($telefono)
        );

        if (!$resultado) {
            return "Ocurrió un error al registrar el entrenador. Intenta de nuevo.";
        }

        return true;
    }

    // ----------------------------------------------------------
    // GET ALL — Obtener todos los entrenadores registrados.
    // Delega directamente a la capa de Datos.
    // Retorna: arreglo de objetos con los datos de los entrenadores.
    // ----------------------------------------------------------
    public function getAll(): array
    {
        return $this->dEntrenador->findAll();
    }

    // ----------------------------------------------------------
    // GET BY ID — Obtener un entrenador específico por su ID.
    // Retorna: objeto con los datos, o null si no existe.
    // ----------------------------------------------------------
    public function getById(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }
        return $this->dEntrenador->findById($id);
    }

    // ----------------------------------------------------------
    // UPDATE — Actualizar los datos del entrenador.
    // Valida los campos antes de delegar a la capa de Datos.
    // Retorna: true si se actualizó, string con error si falló.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $nombre,
        string $correo,
        string $especialidad,
        string $telefono
    ): bool|string {
        if ($id <= 0) {
            return "ID de entrenador no válido.";
        }
        if (empty(trim($nombre))) {
            return "El nombre es obligatorio.";
        }
        if (empty(trim($correo)) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "Se requiere un correo electrónico válido.";
        }
        if (empty(trim($especialidad))) {
            return "La especialidad es obligatoria.";
        }
        if (empty(trim($telefono))) {
            return "El teléfono es obligatorio.";
        }

        $resultado = $this->dEntrenador->update(
            $id,
            trim($nombre),
            trim($correo),
            trim($especialidad),
            trim($telefono)
        );

        if (!$resultado) {
            return "Ocurrió un error al actualizar. Intenta de nuevo.";
        }

        return true;
    }

    // ----------------------------------------------------------
    // DELETE — Eliminar un entrenador del sistema.
    // Valida el ID antes de delegar a la capa de Datos.
    // Retorna: true si se eliminó, string con error si falló.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) {
            return "ID de entrenador no válido.";
        }

        $resultado = $this->dEntrenador->delete($id);

        if (!$resultado) {
            return "Ocurrió un error al eliminar. Intenta de nuevo.";
        }

        return true;
    }
}
