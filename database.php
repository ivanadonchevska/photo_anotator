<?php
class Db {
    private $connection;

    //
    public function __construct() {
        $dbhost = "localhost";
        $dbName = "db_photo";
        $userName = "root";
        $userPassword = "";

        try {
            $this->connection = new PDO("mysql:host=$dbhost;dbname=$dbName", $userName, $userPassword, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>