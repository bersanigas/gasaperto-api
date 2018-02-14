<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 13/02/2018
 * Time: 12:31
 */
class Database{
    private $host       = "localhost";
    private $db_name    = "gaslodi_blodga";
    private $username   = "root";
    private $password   = "";

    private $conn;
    private static $instance;

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance= new static();
        }

        return self::$instance;
    }

    private function __construct() {
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exceptionex) {
            echo "Connection error: " . $exceptionex->getMessage();
        }

        //return $this->conn;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

