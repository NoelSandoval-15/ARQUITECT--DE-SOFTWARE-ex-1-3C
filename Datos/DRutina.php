<?php
// ============================================================
// CAPA DE DATOS — DRutina.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "rutina". Solo SQL, sin validaciones de negocio.
//
// Campos: id, entrenador_id, nombre, tipo
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DRutina
{
    // ----------------------------------------------------------
    // CREATE: Insertar una nueva rutina del entrenador.
    // Retorna: true si insertó, false si hubo error.
    // ----------------------------------------------------------
    public function create(int $entrenador_id, string $nombre, string $tipo): bool
    {
        try {
            $sql = "INSERT INTO rutina (entrenador_id, nombre, tipo)
                    VALUES (:entrenador_id, :nombre, :tipo)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':entrenador_id' => $entrenador_id,
                ':nombre'        => $nombre,
                ':tipo'          => $tipo
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DRutina::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todas las rutinas del entrenador activo.
    // Filtra por entrenador_id para aislar sus datos.
    // Retorna: arreglo de objetos.
    // ----------------------------------------------------------
    public function findAll(int $entrenador_id): array
    {
        try {
            $sql = "SELECT id, nombre, tipo
                    FROM rutina
                    WHERE entrenador_id = :entrenador_id
                    ORDER BY id DESC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':entrenador_id' => $entrenador_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DRutina::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID: Obtener una rutina específica.
    // Verifica que pertenezca al entrenador (seguridad).
    // Retorna: objeto o null.
    // ----------------------------------------------------------
    public function findById(int $id, int $entrenador_id): ?object
    {
        try {
            $sql = "SELECT id, nombre, tipo
                    FROM rutina
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
            error_log("DRutina::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // UPDATE: Actualizar datos de una rutina.
    // Retorna: true si actualizó, false si hubo error.
    // ----------------------------------------------------------
    public function update(int $id, string $nombre, string $tipo): bool
    {
        try {
            $sql = "UPDATE rutina
                    SET nombre = :nombre,
                        tipo   = :tipo
                    WHERE id = :id";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':tipo'   => $tipo,
                ':id'     => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DRutina::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una rutina por su ID.
    // Al eliminarse, rutina_ejercicio asociados se eliminan
    // automáticamente por CASCADE en la BD.
    // Retorna: true si eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM rutina WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DRutina::delete() - Error: " . $e->getMessage());
            return false;
        }
    }
}
