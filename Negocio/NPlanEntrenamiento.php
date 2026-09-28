<?php
// ============================================================
// CAPA DE NEGOCIO — NPlanEntrenamiento.php
// ============================================================
// Responsabilidad ÚNICA: Validaciones del Plan de Entrenamiento.
// Verifica fechas y que todos los IDs sean válidos.
// Sin SQL ni HTML.
// ============================================================

require_once __DIR__ . '/../Datos/DPlanEntrenamiento.php';

class NPlanEntrenamiento
{
    private DPlanEntrenamiento $dPlan;

    public function __construct()
    {
        $this->dPlan = new DPlanEntrenamiento();
    }

    // ----------------------------------------------------------
    // CREATE: Validar y crear un nuevo plan de entrenamiento.
    //
    // Regla de negocio clave: fecha_fin debe ser >= fecha_inicio.
    // Esta validación se hace aquí en Negocio, no en la BD.
    //
    // Retorna: true si creó, string con error si falló.
    // ----------------------------------------------------------
    public function create(
        int    $cliente_id,
        int    $rutina_id,
        int    $dieta_id,
        string $fecha_inicio,
        string $fecha_fin
    ): bool|string {
        if ($cliente_id <= 0) return "Debe seleccionar un cliente válido.";
        if ($rutina_id  <= 0) return "Debe seleccionar una rutina válida.";
        if (empty($fecha_inicio)) return "La fecha de inicio es obligatoria.";
        if (empty($fecha_fin))    return "La fecha de fin es obligatoria.";

        // Regla de negocio: la fecha de fin no puede ser antes que la de inicio
        if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
            return "La fecha de fin debe ser igual o posterior a la fecha de inicio.";
        }

        $dieta_id_final = $dieta_id > 0 ? $dieta_id : null;

        $resultado = $this->dPlan->create(
            $cliente_id, $rutina_id, $dieta_id_final, $fecha_inicio, $fecha_fin
        );
        return $resultado ? true : "Error al crear el plan. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET ALL: Obtener todos los planes del entrenador activo.
    // ----------------------------------------------------------
    public function getAll(int $entrenador_id): array
    {
        if ($entrenador_id <= 0) return [];
        return $this->dPlan->findAll($entrenador_id);
    }

    // ----------------------------------------------------------
    // GET BY ID: Obtener un plan específico.
    // ----------------------------------------------------------
    public function getById(int $id): ?object
    {
        if ($id <= 0) return null;
        return $this->dPlan->findById($id);
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar un plan de entrenamiento.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID de plan no válido.";

        $resultado = $this->dPlan->delete($id);
        return $resultado ? true : "Error al eliminar el plan. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET BY CLIENTE ID: Obtener todos los planes de un cliente.
    // ----------------------------------------------------------
    public function getByClienteId(int $cliente_id): array
    {
        if ($cliente_id <= 0) return [];
        return $this->dPlan->findByClienteId($cliente_id);
    }
}
