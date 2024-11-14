<?php
class ServerConnection
{
    private $server;
    private $user;
    private $password;
    private $database;
    private $connection;
    private $result;
    public $query;
    public $record_count;

    public function __construct()
    {
        // Cambiar estos 4 datos para la conexión a la base de datos
        $this->server = 'localhost';
        $this->user = 'root';
        $this->password = '';
        $this->database = 'bd_midnight';
    }

    // Crear conexión y devolver la conexión
    public function create_connection()
    {
        $this->connection = new mysqli($this->server, $this->user, $this->password, $this->database);

        if ($this->connection->connect_error) {
            // Registra el error en un archivo de log y muestra un mensaje genérico
            error_log("Error de conexión a la base de datos: " . $this->connection->connect_error);
            die('<h3 style="color: tomato; font-family: Arial;">No se puede establecer conexión</h3>');
        }

        return $this->connection;  // Regresa la conexión
    }

    // Cerrar la conexión
    private function close_connection()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    // Ejecutar una consulta (no se espera un valor de retorno)
    public function execute_query()
    {
        $this->result = $this->create_connection()->query($this->query);

        if (!$this->result) {
            // Registra el error de consulta si ocurre
            error_log("Error de consulta: " . $this->connection->error);
        }

        $this->close_connection();
        return $this->result;
    }

    // Obtener todos los registros de la consulta
    public function get_records()
    {
        $this->result = $this->create_connection()->query($this->query);

        if (!$this->result) {
            // Registra el error de consulta si ocurre
            error_log("Error de consulta: " . $this->connection->error);
        }

        $this->record_count = $this->result->num_rows;
        $records = $this->result->fetch_all(MYSQLI_ASSOC);  // Obtener todos los registros como un array
        $this->result->free();
        $this->close_connection();
        return $records;
    }

    // Usar consultas preparadas para evitar inyección SQL
    public function execute_prepared_query($sql, $params = [])
    {
        $stmt = $this->create_connection()->prepare($sql);

        if ($stmt === false) {
            // Registra el error si no se puede preparar la consulta
            error_log("Error al preparar la consulta: " . $this->connection->error);
            return false;
        }

        // Asocia los parámetros
        if ($params) {
            $stmt->bind_param(...$params);
        }

        $stmt->execute();
        $this->close_connection();
        return $stmt;
    }
}
?>
