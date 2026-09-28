<?php
// ============================================================
// CAPA DE DATOS — DEntrenador.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "entrenador" en la base de datos PostgreSQL.
//
// REGLAS que esta clase SIEMPRE debe respetar:
//   ✔ Solo contiene consultas SQL (INSERT, SELECT, UPDATE, DELETE).
//   ✔ Usa la conexión provista por DBHelper.
//   ✔ Devuelve datos crudos (objetos PDO) a la capa de Negocio.
//   ✘ No valida reglas de negocio (eso es responsabilidad de NEntrenador).
//   ✘ No genera HTML ni interactúa con el usuario.
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DEntrenador
{
    // ----------------------------------------------------------
    // INSERTAR un nuevo entrenador en la base de datos.
    // Recibe los datos ya validados desde la capa de Negocio.
    // Retorna: true si se insertó correctamente, false si hubo error.
    // ----------------------------------------------------------
    public function create(
        string $nombre,
        string $correo,
        string $password,
        string $especialidad,
        string $telefono
    ): bool {
        try {
            $sql = "INSERT INTO entrenador (nombre, correo, password, especialidad, telefono)
                    VALUES (:nombre, :correo, :password, :especialidad, :telefono)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);

            // Usamos parámetros nombrados (:nombre) para evitar inyección SQL
            $stmt->execute([
                ':nombre'      => $nombre,
                ':correo'      => $correo,
                ':password'    => $password,
                ':especialidad'=> $especialidad,
                ':telefono'    => $telefono
            ]);

            return true;
        } catch (PDOException $e) {
            // Solo registramos el error técnico; el Negocio decide qué hacer
            error_log("DEntrenador::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // BUSCAR un entrenador por su correo electrónico.
    // Se usa principalmente para el proceso de Login.
    // Retorna: objeto con los datos del entrenador, o null si no existe.
    // ----------------------------------------------------------
    public function findByCorreo(string $correo): ?object
    {
        try {
            $sql  = "SELECT * FROM entrenador WHERE correo = :correo LIMIT 1";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':correo' => $correo]);

            // fetch() devuelve un objeto o false si no encontró nada
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DEntrenador::findByCorreo() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // OBTENER todos los entrenadores registrados.
    // Retorna: arreglo de objetos con todos los registros.
    // ----------------------------------------------------------
    public function findAll(): array
    {
        try {
            $sql  = "SELECT id, nombre, correo, especialidad, telefono
                     FROM entrenador
                     ORDER BY nombre ASC";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // fetchAll() devuelve todos los registros como arreglo de objetos
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DEntrenador::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // BUSCAR un entrenador por su ID.
    // Retorna: objeto con los datos, o null si no existe.
    // ----------------------------------------------------------
    public function findById(int $id): ?object
    {
        try {
            $sql  = "SELECT id, nombre, correo, especialidad, telefono
                     FROM entrenador
                     WHERE id = :id LIMIT 1";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DEntrenador::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // ACTUALIZAR los datos de un entrenador existente.
    // El password NO se actualiza aquí (operación separada por seguridad).
    // Retorna: true si se actualizó, false si hubo error.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $nombre,
        string $correo,
        string $especialidad,
        string $telefono
    ): bool {
        try {
            $sql = "UPDATE entrenador
                    SET nombre = :nombre,
                        correo = :correo,
                        especialidad = :especialidad,
                        telefono = :telefono
                    WHERE id = :id";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre'      => $nombre,
                ':correo'      => $correo,
                ':especialidad'=> $especialidad,
                ':telefono'    => $telefono,
                ':id'          => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DEntrenador::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // ELIMINAR un entrenador por su ID.
    // Retorna: true si se eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM entrenador WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DEntrenador::delete() - Error: " . $e->getMessage());
            return false;
        }
    }
}
