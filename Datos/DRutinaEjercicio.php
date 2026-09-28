<?php
// ============================================================
// CAPA DE DATOS — DRutinaEjercicio.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "rutina_ejercicio". Tabla intermedia M:N entre
// rutina y ejercicio.
//
// Campos: id, rutina_id, ejercicio_id, dia_rutina,
//         series, repeticiones, duracion, descanso
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DRutinaEjercicio
{
    // ----------------------------------------------------------
    // CREATE: Asignar un ejercicio a una rutina con sus detalles.
    // Retorna: true si insertó, false si hubo error.
    // ----------------------------------------------------------
    public function create(
        int    $rutina_id,
        int    $ejercicio_id,
        string $dia_rutina,
        int    $series,
        int    $repeticiones,
        int    $duracion,
        int    $descanso
    ): bool {
        try {
            $sql = "INSERT INTO rutina_ejercicio
                        (rutina_id, ejercicio_id, dia_rutina, series, repeticiones, duracion, descanso)
                    VALUES
                        (:rutina_id, :ejercicio_id, :dia_rutina, :series, :repeticiones, :duracion, :descanso)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':rutina_id'    => $rutina_id,
                ':ejercicio_id' => $ejercicio_id,
                ':dia_rutina'   => $dia_rutina,
                ':series'       => $series,
                ':repeticiones' => $repeticiones,
                ':duracion'     => $duracion,
                ':descanso'     => $descanso
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DRutinaEjercicio::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL BY RUTINA: Obtener todos los ejercicios asignados
    // a una rutina específica. Hace JOIN con ejercicio para
    // mostrar el nombre del ejercicio junto a sus detalles.
    // Retorna: arreglo de objetos.
    // ----------------------------------------------------------
    public function findAllByRutina(int $rutina_id): array
    {
        try {
            $sql = "SELECT re.id,
                           re.dia_rutina,
                           re.series,
                           re.repeticiones,
                           re.duracion,
                           re.descanso,
                           e.nombre AS ejercicio_nombre,
                           e.imagen_url,
                           e.video_url
                    FROM rutina_ejercicio re
                    INNER JOIN ejercicio e ON e.id = re.ejercicio_id
                    WHERE re.rutina_id = :rutina_id
                    ORDER BY re.dia_rutina ASC, re.id ASC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':rutina_id' => $rutina_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DRutinaEjercicio::findAllByRutina() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todas las asignaciones (con JOIN).
    // Retorna: arreglo de objetos.
    // ----------------------------------------------------------
    public function findAll(): array
    {
        try {
            $sql = "SELECT re.id,
                           re.rutina_id,
                           re.dia_rutina,
                           re.series,
                           re.repeticiones,
                           re.duracion,
                           re.descanso,
                           e.nombre AS ejercicio_nombre,
                           r.nombre AS rutina_nombre
                    FROM rutina_ejercicio re
                    INNER JOIN ejercicio e ON e.id = re.ejercicio_id
                    INNER JOIN rutina    r ON r.id = re.rutina_id
                    ORDER BY r.nombre ASC, re.dia_rutina ASC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->query($sql);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DRutinaEjercicio::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una asignación rutina-ejercicio por su ID.
    // Retorna: true si eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM rutina_ejercicio WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DRutinaEjercicio::delete() - Error: " . $e->getMessage());
            return false;
        }
    }
}
