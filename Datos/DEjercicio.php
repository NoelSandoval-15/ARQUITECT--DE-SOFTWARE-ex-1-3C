<?php
// ============================================================
// CAPA DE DATOS — DEjercicio.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "ejercicio". Solo SQL, sin validaciones de negocio.
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DEjercicio
{
    // ----------------------------------------------------------
    // CREATE: Insertar un nuevo ejercicio
    // ----------------------------------------------------------
    public function create(string $nombre, string $duracion, string $repeticion, string $imagen_url, string $video_url): bool
    {
        try {
            $sql = "INSERT INTO ejercicio (nombre, duracion, repeticion, imagen_url, video_url)
                    VALUES (:nombre, :duracion, :repeticion, :imagen_url, :video_url)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre'     => $nombre,
                ':duracion'   => $duracion,
                ':repeticion' => $repeticion,
                ':imagen_url' => $imagen_url,
                ':video_url'  => $video_url
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DEjercicio::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todos los ejercicios
    // ----------------------------------------------------------
    public function findAll(): array
    {
        try {
            $sql = "SELECT * FROM ejercicio ORDER BY id DESC";
            $conn = DBHelper::createConnection();
            $stmt = $conn->query($sql);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DEjercicio::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID: Obtener un ejercicio específico
    // ----------------------------------------------------------
    public function findById(int $id): ?object
    {
        try {
            $sql = "SELECT * FROM ejercicio WHERE id = :id LIMIT 1";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DEjercicio::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // UPDATE: Actualizar datos de un ejercicio
    // ----------------------------------------------------------
    public function update(int $id, string $nombre, string $duracion, string $repeticion, string $imagen_url, string $video_url): bool
    {
        try {
            $sql = "UPDATE ejercicio
                    SET nombre     = :nombre,
                        duracion   = :duracion,
                        repeticion = :repeticion,
                        imagen_url = :imagen_url,
                        video_url  = :video_url
                    WHERE id = :id";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre'     => $nombre,
                ':duracion'   => $duracion,
                ':repeticion' => $repeticion,
                ':imagen_url' => $imagen_url,
                ':video_url'  => $video_url,
                ':id'         => $id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DEjercicio::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar un ejercicio
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM ejercicio WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DEjercicio::delete() - Error: " . $e->getMessage());
            return false;
        }
    }
}
