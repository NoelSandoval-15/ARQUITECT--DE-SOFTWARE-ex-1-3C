<?php
// ============================================================
// CAPA DE DATOS — DPlanEntrenamiento.php
// ============================================================
// Responsabilidad ÚNICA: Ejecutar operaciones SQL sobre la
// tabla "plan_entrenamiento". Es la tabla transaccional
// que une cliente + rutina + dieta con fechas.
//
// Campos: id, cliente_id, rutina_id, dieta_id,
//         fecha_inicio, fecha_fin
// ============================================================

require_once __DIR__ . '/../config/DBHelper.php';

class DPlanEntrenamiento
{
    // ----------------------------------------------------------
    // CREATE: Insertar un nuevo plan de entrenamiento.
    // Retorna: true si insertó, false si hubo error.
    // ----------------------------------------------------------
    public function create(
        int    $cliente_id,
        int    $rutina_id,
        ?int   $dieta_id,
        string $fecha_inicio,
        string $fecha_fin
    ): bool {
        try {
            $sql = "INSERT INTO plan_entrenamiento
                        (cliente_id, rutina_id, dieta_id, fecha_inicio, fecha_fin)
                    VALUES
                        (:cliente_id, :rutina_id, :dieta_id, :fecha_inicio, :fecha_fin)";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':cliente_id'   => $cliente_id,
                ':rutina_id'    => $rutina_id,
                ':dieta_id'     => $dieta_id,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin'    => $fecha_fin
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("DPlanEntrenamiento::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND ALL: Obtener todos los planes con datos relacionados.
    // Hace JOIN con cliente, rutina y dieta para mostrar
    // información significativa en la vista de lista.
    // Retorna: arreglo de objetos con datos combinados.
    // ----------------------------------------------------------
    public function findAll(int $entrenador_id): array
    {
        try {
            // JOIN con cliente, rutina y dieta para mostrar datos completos
            $sql = "SELECT pe.id,
                           pe.fecha_inicio,
                           pe.fecha_fin,
                           c.nombre || ' ' || c.apellido AS cliente_nombre,
                           c.telefono,
                           c.correo,
                           r.nombre                       AS rutina_nombre,
                           r.tipo                         AS rutina_tipo,
                           d.titulo                       AS dieta_titulo,
                           d.titulo                       AS dieta_nombre
                    FROM plan_entrenamiento pe
                    INNER JOIN cliente c ON c.id = pe.cliente_id
                    INNER JOIN rutina  r ON r.id = pe.rutina_id
                    LEFT JOIN dieta    d ON d.id = pe.dieta_id
                    WHERE r.entrenador_id = :entrenador_id
                    ORDER BY pe.fecha_inicio DESC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':entrenador_id' => $entrenador_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DPlanEntrenamiento::findAll() - Error: " . $e->getMessage());
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND BY ID: Obtener un plan específico por su ID.
    // Retorna: objeto o null.
    // ----------------------------------------------------------
    public function findById(int $id): ?object
    {
        try {
            $sql = "SELECT pe.id,
                           pe.cliente_id,
                           pe.rutina_id,
                           pe.dieta_id,
                           pe.fecha_inicio,
                           pe.fecha_fin,
                           c.nombre  || ' ' || c.apellido AS cliente_nombre,
                           r.nombre                        AS rutina_nombre,
                           d.titulo                        AS dieta_titulo
                    FROM plan_entrenamiento pe
                    INNER JOIN cliente c ON c.id = pe.cliente_id
                    INNER JOIN rutina  r ON r.id = pe.rutina_id
                    LEFT JOIN dieta    d ON d.id = pe.dieta_id
                    WHERE pe.id = :id
                    LIMIT 1";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("DPlanEntrenamiento::findById() - Error: " . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar un plan de entrenamiento por su ID.
    // Retorna: true si eliminó, false si hubo error.
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        try {
            $sql  = "DELETE FROM plan_entrenamiento WHERE id = :id";
            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            error_log("DPlanEntrenamiento::delete() - Error: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------
    // FIND BY CLIENTE ID: Obtener todos los planes de un cliente.
    // ----------------------------------------------------------
    public function findByClienteId(int $cliente_id): array
    {
        try {
            $sql = "SELECT pe.id,
                           pe.fecha_inicio,
                           pe.fecha_fin,
                           c.nombre || ' ' || c.apellido AS cliente_nombre,
                           r.nombre                       AS rutina_nombre,
                           r.tipo                         AS rutina_tipo,
                           d.titulo                       AS dieta_titulo,
                           d.titulo                       AS dieta_nombre
                    FROM plan_entrenamiento pe
                    INNER JOIN cliente c ON c.id = pe.cliente_id
                    INNER JOIN rutina  r ON r.id = pe.rutina_id
                    LEFT JOIN dieta    d ON d.id = pe.dieta_id
                    WHERE pe.cliente_id = :cliente_id
                    ORDER BY pe.fecha_inicio DESC";

            $conn = DBHelper::createConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([':cliente_id' => $cliente_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("DPlanEntrenamiento::findByClienteId() - Error: " . $e->getMessage());
            return [];
        }
    }
}
