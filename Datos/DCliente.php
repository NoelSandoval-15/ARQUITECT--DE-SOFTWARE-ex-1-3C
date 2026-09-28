<?php
// ============================================================
// CAPA DE DATOS — DCliente.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "cliente" en la base de datos PostgreSQL.
//
// Campos de la tabla cliente:
//   id, nombre, correo, telefono, sexo, altura, peso, password
//
// REGLAS:
//   ✔ Solo contiene SQL (INSERT, SELECT, UPDATE, DELETE).
//   ✔ Usa DBHelper para obtener la conexión.
//   ✘ No valida reglas de negocio (eso es NCliente).
//   ✘ No genera HTML.
//
// NOTA FUTURA: El campo "password" se usará cuando se
//   implemente el portal de acceso del cliente.
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DCliente
{
    // ----------------------------------------------------------
    // CREATE: Insertar un nuevo cliente en la base de datos.
    // El entrenador_id no es un campo directo en la tabla cliente
    // según el nuevo diseño, pero el cliente queda vinculado
    // al entrenador a través del plan_entrenamiento.
    // Retorna: true si se insertó, false si hubo error.
    // ----------------------------------------------------------
    public function create(
        string $nombre,
        string $apellido,
        string $correo,
        string $telefono,
        string $sexo,
        float  $altura,
        float  $peso,
        string $password
    ): bool {
        try {
            $sql = "INSERT INTO cliente
                        (nombre, apellido, correo, telefono, sexo, altura, peso, password)
                    VALUES
                        (:nombre, :apellido, :correo, :telefono, :sexo, :altura, :peso, :password)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':correo'   => $correo,
                ':telefono' => $telefono,
                ':sexo'     => $sexo,
                ':altura'   => $altura,
                ':peso'     => $peso,
                ':password' => $password
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DCliente::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todos los clientes registrados.
    // Retorna: arreglo de objetos con los registros.
    // ----------------------------------------------------------
    public function findAll(): array
    {
        try {
            $sql  = "SELECT id, nombre, apellido, correo, telefono, sexo, altura, peso
                     FROM cliente
                     ORDER BY apellido ASC, nombre ASC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DCliente::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID: Obtener un cliente específico por su ID.
    // Retorna: objeto con los datos, o null si no existe.
    // ----------------------------------------------------------
    public function findById(int $id): ?object
    {
        try {
            $sql  = "SELECT id, nombre, apellido, correo, telefono, sexo, altura, peso
                     FROM cliente
                     WHERE id = :id
                     LIMIT 1";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DCliente::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // UPDATE: Actualizar datos de un cliente existente.
    // El password NO se actualiza aquí (operación separada).
    // Retorna: true si se actualizó, false si hubo error.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $nombre,
        string $apellido,
        string $correo,
        string $telefono,
        string $sexo,
        float  $altura,
        float  $peso
    ): bool {
        try {
            $sql = "UPDATE cliente
                    SET nombre    = :nombre,
                        apellido  = :apellido,
                        correo    = :correo,
                        telefono  = :telefono,
                        sexo      = :sexo,
                        altura    = :altura,
                        peso      = :peso
                    WHERE id = :id";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':correo'   => $correo,
                ':telefono' => $telefono,
                ':sexo'     => $sexo,
                ':altura'   => $altura,
                ':peso'     => $peso,
                ':id'       => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DCliente::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar un cliente por su ID.
    // Retorna: true si se eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM cliente WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DCliente::delete() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND BY CORREO: Obtener un cliente por su correo.
    // Retorna: objeto con los datos, o null si no existe.
    // ----------------------------------------------------------
    public function findByCorreo(string $correo): ?object
    {
        try {
            $sql  = "SELECT id, nombre, apellido, correo, telefono, sexo, altura, peso, password
                     FROM cliente
                     WHERE correo = :correo
                     LIMIT 1";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':correo' => $correo]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DCliente::findByCorreo() - Error: " . $e->getMessage());
            return null;
        }
    }
}
