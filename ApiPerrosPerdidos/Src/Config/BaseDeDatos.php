<?php

if (!defined('INCLUDED')) {
    die("Acceso no permitido.");
}

require_once 'helpers.php';

class BaseDeDatos {

    private $conn;

    public function getConnection() {
        $this->conn = null;

        $config = include __DIR__ . '/config.php';

        $host = $config['database']['host'];
        $db_name = $config['database']['db_name'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];

        try {
            $this->conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            handlePdoError($exception);
        }

        return $this->conn;
    }
}

?>