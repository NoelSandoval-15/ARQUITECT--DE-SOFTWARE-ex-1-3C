<?php
// ============================================================
// CAPA DE NEGOCIO — NDieta.php
// ============================================================
// Responsabilidad ÚNICA: Aplicar validaciones y reglas de
// negocio del módulo Dieta antes de delegar a la capa Datos.
//
// REGLAS:
//   ✔ Valida que titulo y descripcion no estén vacíos.
//   ✔ Usa el entrenador_id de la sesión para filtrar y asociar.
//   ✔ Se comunica HACIA ABAJO solo con DDieta.
//   ✘ No contiene SQL.
//   ✘ No genera HTML.
// ============================================================

require_once __DIR__ . '/../Datos/DDieta.php';

class NDieta
{
    // Instancia de la capa de Datos
    private DDieta $dDieta;

    public function __construct()
    {
        $this->dDieta = new DDieta();
    }

    // ----------------------------------------------------------
    // CREATE: Validar y registrar una nueva dieta.
    //
    // El entrenador_id proviene de la sesión activa, lo que
    // garantiza que la dieta quede vinculada al entrenador correcto.
    //
    // Retorna: true si se creó, string con error si falló.
    // ----------------------------------------------------------
    public function create(
        int    $entrenador_id,
        string $titulo,
        string $descripcion
    ): bool|string {
        // --- Validaciones de campos obligatorios ---
        if (empty(trim($titulo))) {
            return "El título de la dieta es obligatorio.";
        }
        if (strlen(trim($titulo)) < 3) {
            return "El título debe tener al menos 3 caracteres.";
        }
        if (empty(trim($descripcion))) {
            return "La descripción de la dieta es obligatoria.";
        }
        if ($entrenador_id <= 0) {
            return "No se detectó una sesión de entrenador válida.";
        }

        // --- Delegar inserción a la capa de Datos ---
        $resultado = $this->dDieta->create(
            $entrenador_id,
            trim($titulo),
            trim($descripcion)
        );

        return $resultado ? true : "Error al registrar la dieta. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET ALL: Obtener todas las dietas del entrenador activo.
    // Retorna: arreglo de objetos con las dietas.
    // ----------------------------------------------------------
    public function getAll(int $entrenador_id): array
    {
        if ($entrenador_id <= 0) return [];
        return $this->dDieta->findAll($entrenador_id);
    }

    // ----------------------------------------------------------
    // GET BY ID: Obtener una dieta específica del entrenador.
    // Retorna: objeto con datos, o null si no existe.
    // ----------------------------------------------------------
    public function getById(int $id, int $entrenador_id): ?object
    {
        if ($id <= 0 || $entrenador_id <= 0) return null;
        return $this->dDieta->findById($id, $entrenador_id);
    }

    // ----------------------------------------------------------
    // UPDATE: Validar y actualizar una dieta.
    // Retorna: true si actualizó, string con error si falló.
    // ----------------------------------------------------------
    public function update(
        int    $id,
        string $titulo,
        string $descripcion
    ): bool|string {
        if ($id <= 0) return "ID de dieta no válido.";
        if (empty(trim($titulo))) {
            return "El título de la dieta es obligatorio.";
        }
        if (strlen(trim($titulo)) < 3) {
            return "El título debe tener al menos 3 caracteres.";
        }
        if (empty(trim($descripcion))) {
            return "La descripción de la dieta es obligatoria.";
        }

        $resultado = $this->dDieta->update(
            $id,
            trim($titulo),
            trim($descripcion)
        );

        return $resultado ? true : "Error al actualizar la dieta. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // DELETE: Eliminar una dieta del sistema.
    // Retorna: true si eliminó, string con error si falló.
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID de dieta no válido.";

        $resultado = $this->dDieta->delete($id);
        return $resultado ? true : "Error al eliminar la dieta. Intenta de nuevo.";
    }

    // ----------------------------------------------------------
    // GET BY ID FOR CLIENT: Obtener una dieta para un cliente.
    // ----------------------------------------------------------
    public function getByIdForClient(int $id): ?object
    {
        if ($id <= 0) return null;
        return $this->dDieta->findByIdForClient($id);
    }
}
