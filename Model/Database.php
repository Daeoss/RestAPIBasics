<?php
class Database {
    protected $connection = null;

    public function __construct() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            if(mysqli_connect_errno()) {
                throw new Exception("Could not connect to the database.");
            } 
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function select($query = "", $params = [], $format = "") {
        try {
            $stmt = $this->executeStatement($query, $params, $format);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }   
        return false;    
    }

    public function query($query = "", $params = [], $format = "", $message = "") {
        try {
            $stmt = $this->executeStatement($query, $params, $format);
            $stmt->close();
            return ["User's data $message successfully!"];
        } catch(Exception $e) {
            throw new Exception("Could not perform the action: " . $e->getMessage());
        }    
    }

    public function executeStatement($query = "", $params = [], $format = "") {
        try {
            $stmt = $this->connection->prepare($query);
            if($stmt === false) {
                throw new Exception("Unable to do prepared statement: ". $query);
            }

            if ($params) {
                $stmt->bind_param($format, ...$params);
            }
            $stmt->execute();
            return $stmt;
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}