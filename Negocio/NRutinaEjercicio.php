<?php
// ============================================================
// CAPA DE NEGOCIO — NRutinaEjercicio.php
// ============================================================
// Responsabilidad ÚNICA: Validaciones para asignar ejercicios
// a una rutina. Verifica rangos numéricos y campos vacíos.
// Sin SQL ni HTML.
// ============================================================

require_once __DIR__ . '/../Datos/DRutinaEjercicio.php';

class NRutinaEjercicio
{
    private DRutinaEjercicio $dRutinaEjercicio;

    public function __construct()
    {
        $this->dRutinaEjercicio = new DRutinaEjercicio();
    }

    // ----------------------------------------------------------
    // CREATE: Validar y asignar un ejercicio a una rutina.
    // series, repeticiones, duracion, descanso deben ser > 0.
    // Retorna: true si creó, string con error si falló.
    // ----------------------------------------------------------
    public function create(
        int    $rutina_id,
        int    $ejercicio_id,
        string $dia_rutina,
        int    $series,
        int    $repeticiones,
        int    $duracion,
        int    $descanso
    ): bool|string {
        if ($rutina_id <= 0)    return "Debe seleccionar una rutina válida.";
        if ($ejercicio_id <= 0) return "Debe seleccionar un ejercicio válido.";
        if (empty(trim($dia_rutina))) return "El día de la rutina es obligatorio.";
        if ($series <= 0)       return "Las series deben ser un número mayor a 0.";
        if ($repeticiones <= 0) return "Las repeticiones deben ser un número mayor a 0.";
        if ($duracion <= 0)     return "La duración debe ser un número mayor a 0 segundos.";
        if ($descanso < 0)      return "El descanso no puede ser negativo.";

        $resultado = $this->dRutinaEjercicio->create(
            $rutina_id, $ejercicio_id, trim($dia_rutina),
            $series, $repeticiones, $duracion, $descanso
        );
        return $resultado ? true : "Error al asignar el ejercicio. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET ALL BY RUTINA: Obtener todos los ejercicios de una rutina.
    // ----------------------------------------------------------
    public function getByRutina(int $rutina_id): array
    {
        if ($rutina_id <= 0) return [];
        return $this->dRutinaEjercicio->findAllByRutina($rutina_id);
    }

    // ----------------------------------------------------------
    // GET ALL: Obtener todas las asignaciones.
    // ----------------------------------------------------------
    public function getAll(): array
    {
        return $this->dRutinaEjercicio->findAll();
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una asignación rutina-ejercicio.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID no válido.";

        $resultado = $this->dRutinaEjercicio->delete($id);
        return $resultado ? true : "Error al eliminar la asignación. Intenta de nuevo.";
    }
}
