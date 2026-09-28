<?php
// ============================================================
// CAPA DE NEGOCIO — NRutina.php
// ============================================================
// Responsabilidad ÚNICA: Validaciones y reglas de negocio
// del módulo Rutina. Sin SQL ni HTML.
// ============================================================

require_once __DIR__ . '/../Datos/DRutina.php';

class NRutina
{
    private DRutina $dRutina;

    public function __construct()
    {
        $this->dRutina = new DRutina();
    }

    // ----------------------------------------------------------
    // CREATE: Validar y crear una nueva rutina.
    // Tipos válidos: Fuerza, Cardio, Flexibilidad, Mixta, Hipertrofia
    // Retorna: true si creó, string con error si falló.
    // ----------------------------------------------------------
    public function create(int $entrenador_id, string $nombre, string $tipo): bool|string
    {
        if ($entrenador_id <= 0) return "Sesión de entrenador no válida.";
        if (empty(trim($nombre))) return "El nombre de la rutina es obligatorio.";
        if (strlen(trim($nombre)) < 3) return "El nombre debe tener al menos 3 caracteres.";
        if (empty(trim($tipo))) return "El tipo de rutina es obligatorio.";

        $resultado = $this->dRutina->create($entrenador_id, trim($nombre), trim($tipo));
        return $resultado ? true : "Error al crear la rutina. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET ALL: Obtener todas las rutinas del entrenador.
    // ----------------------------------------------------------
    public function getAll(int $entrenador_id): array
    {
        if ($entrenador_id <= 0) return [];
        return $this->dRutina->findAll($entrenador_id);
    }

    // ----------------------------------------------------------
    // GET BY ID: Obtener una rutina verificando que sea del entrenador.
    // ----------------------------------------------------------
    public function getById(int $id, int $entrenador_id): ?object
    {
        if ($id <= 0 || $entrenador_id <= 0) return null;
        return $this->dRutina->findById($id, $entrenador_id);
    }

    // ----------------------------------------------------------
    // UPDATE: Validar y actualizar una rutina.
    // ----------------------------------------------------------
    public function update(int $id, string $nombre, string $tipo): bool|string
    {
        if ($id <= 0) return "ID de rutina no válido.";
        if (empty(trim($nombre))) return "El nombre de la rutina es obligatorio.";
        if (strlen(trim($nombre)) < 3) return "El nombre debe tener al menos 3 caracteres.";
        if (empty(trim($tipo))) return "El tipo de rutina es obligatorio.";

        $resultado = $this->dRutina->update($id, trim($nombre), trim($tipo));
        return $resultado ? true : "Error al actualizar la rutina. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una rutina.
    // Al eliminarse, sus ejercicios asignados se borran por CASCADE.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID de rutina no válido.";

        $resultado = $this->dRutina->delete($id);
        return $resultado ? true : "Error al eliminar la rutina. Intenta de nuevo.";
    }
}
