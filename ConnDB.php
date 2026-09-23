<?php

class ConnDB
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        $this->host = getenv('MYSQLHOST') ?: 'localhost';
        $this->db_name = getenv('MYSQLDATABASE') ?: 'cdb';
        $this->username = getenv('MYSQLUSER') ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: '';
        $this->port = getenv('MYSQLPORT') ?: '3306';

        try {

            $dsn = "mysql:host=" . $this->host
                . ";port=" . $this->port
                . ";dbname=" . $this->db_name
                . ";charset=utf8mb4";

            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $exception) {

            header("Content-Type: application/json; charset=UTF-8");

            echo json_encode([
                "status" => "error",
                "message" => "Connection error: " . $exception->getMessage()
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        return $this->conn;
    }
}