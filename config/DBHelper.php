<?php
// ============================================================
// CAPA DE CONFIGURACIÓN
// Archivo: DBHelper.php
// Responsabilidad: Gestionar la conexión a la base de datos
//                  PostgreSQL. No contiene lógica de negocio
//                  ni presentación.
// ============================================================

class DBHelper
{
    private static string $host     = "localhost";
    private static string $puerto   = "5432";
    private static string $nombrebd = "Noel-Arqui-GYM";
    private static string $usuario  = "postgres";
    private static string $clave    = "2002";

    public static function createConnection(): PDO
    {
        try {
            $dsn = "pgsql:host=" . self::$host .
                   ";port=" . self::$puerto .
                   ";dbname=" . self::$nombrebd;

            $conexion = new PDO($dsn, self::$usuario, self::$clave);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
