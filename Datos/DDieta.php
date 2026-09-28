<?php
// ============================================================
// CAPA DE DATOS — DDieta.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "dieta" en la base de datos PostgreSQL.
//
// Campos de la tabla dieta:
//   id, entrenador_id, titulo, descripcion
//
// REGLAS:
//   ✔ Solo contiene SQL (INSERT, SELECT, UPDATE, DELETE).
//   ✔ Usa DBHelper para obtener la conexión.
//   ✔ Filtra siempre por entrenador_id para aislar los datos.
//   ✘ No valida reglas de negocio (responsabilidad de NDieta).
//   ✘ No genera HTML.
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DDieta
{
    // ----------------------------------------------------------
    // CREATE: Insertar una nueva dieta diseñada por el entrenador.
    // El entrenador_id vincula la dieta con quien la creó.
    // Retorna: true si se insertó, false si hubo error.
    // ----------------------------------------------------------
    public function create(
        int    $entrenador_id,
        string $titulo,
        string $descripcion
    ): bool {
        try {
            $sql = "INSERT INTO dieta (entrenador_id, titulo, descripcion)
                    VALUES (:entrenador_id, :titulo, :descripcion)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':entrenador_id' => $entrenador_id,
                ':titulo'        => $titulo,
                ':descripcion'   => $descripcion
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DDieta::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todas las dietas del entrenador activo.
    // Filtramos por entrenador_id para que cada entrenador
    // solo vea SUS propias dietas.
    // Retorna: arreglo de objetos con todos los registros.
    // ----------------------------------------------------------
    public function findAll(int $entrenador_id): array
    {
        try {
            $sql = "SELECT id, titulo, descripcion
                    FROM dieta
                    WHERE entrenador_id = :entrenador_id
                    ORDER BY id DESC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':entrenador_id' => $entrenador_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DDieta::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID: Obtener una dieta específica.
    // También verifica que pertenezca al entrenador (seguridad).
    // Retorna: objeto con los datos, o null si no existe.
    // ----------------------------------------------------------
    public function findById(int $id, int $entrenador_id): ?object
    {
        try {
            $sql = "SELECT id, titulo, descripcion
                    FROM dieta
                    WHERE id = :id AND entrenador_id = :entrenador_id
                    LIMIT 1";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id'            => $id,
                ':entrenador_id' => $entrenador_id
            ]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DDieta::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // UPDATE: Actualizar una dieta existente.
    // Retorna: true si se actualizó, false si hubo error.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $titulo,
        string $descripcion
    ): bool {
        try {
            $sql = "UPDATE dieta
                    SET titulo      = :titulo,
                        descripcion = :descripcion
                    WHERE id = :id";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':titulo'      => $titulo,
                ':descripcion' => $descripcion,
                ':id'          => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DDieta::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una dieta por su ID.
    // Retorna: true si se eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM dieta WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DDieta::delete() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID FOR CLIENT: Obtener una dieta por ID (solo lectura).
    // Usado por el portal de clientes, no requiere entrenador_id.
    // ----------------------------------------------------------
    public function findByIdForClient(int $id): ?object
    {
        try {
            $sql = "SELECT id, titulo, descripcion
                    FROM dieta
                    WHERE id = :id
                    LIMIT 1";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DDieta::findByIdForClient() - Error: " . $e->getMessage());
            return null;
        }
    }
}
