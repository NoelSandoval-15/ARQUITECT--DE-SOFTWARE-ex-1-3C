<?php
// ============================================================
// CAPA DE NEGOCIO — NEjercicio.php
// ============================================================
// Responsabilidad ÚNICA: Validar datos antes de enviarlos a
// la capa de datos. Sin SQL ni HTML.
// ============================================================

require_once __DIR__ . '/../Datos/DEjercicio.php';

class NEjercicio
{
    private DEjercicio $dEjercicio;

    public function __construct()
    {
        $this->dEjercicio = new DEjercicio();
    }

    // ----------------------------------------------------------
    // CREATE: Valida y delega la creación
    // ----------------------------------------------------------
    public function create(string $nombre, string $duracion, string $repeticion, string $imagen_url, string $video_url): bool|string
    {
        if (empty(trim($nombre)))     return "El nombre del ejercicio es obligatorio.";
        if (empty(trim($duracion)))   return "La duraciÃ³n es obligatoria.";
        if (empty(trim($repeticion))) return "Las repeticiones son obligatorias.";
        // imagen_url y video_url son opcionales

        $resultado = $this->dEjercicio->create(trim($nombre), trim($duracion), trim($repeticion), trim($imagen_url), trim($video_url));
        return $resultado ? true : "Error al registrar el ejercicio en la base de datos.";
    }

    // ----------------------------------------------------------
    // GET ALL: Delega la consulta de todos los registros
    // ----------------------------------------------------------
    public function getAll(): array
    {
        return $this->dEjercicio->findAll();
    }

    // ----------------------------------------------------------
    // GET BY ID: Delega la consulta por ID
    // ----------------------------------------------------------
    public function getById(int $id): ?object
    {
        if ($id <= 0) return null;
        return $this->dEjercicio->findById($id);
    }

    // ----------------------------------------------------------
    // UPDATE: Valida y delega la actualización
    // ----------------------------------------------------------
    public function update(int $id, string $nombre, string $duracion, string $repeticion, string $imagen_url, string $video_url): bool|string
    {
        if ($id <= 0)                 return "ID no vÃ¡lido.";
        if (empty(trim($nombre)))     return "El nombre del ejercicio es obligatorio.";
        if (empty(trim($duracion)))   return "La duraciÃ³n es obligatoria.";
        if (empty(trim($repeticion))) return "Las repeticiones son obligatorias.";

        $resultado = $this->dEjercicio->update($id, trim($nombre), trim($duracion), trim($repeticion), trim($imagen_url), trim($video_url));
        return $resultado ? true : "Error al actualizar el ejercicio.";
    }

    // ----------------------------------------------------------
    // DELETE: Valida y delega la eliminación
    // ----------------------------------------------------------
    public function delete(int $id): bool|string
    {
        if ($id <= 0) return "ID no válido.";
        
        $resultado = $this->dEjercicio->delete($id);
        return $resultado ? true : "Error al eliminar el ejercicio.";
    }
}
