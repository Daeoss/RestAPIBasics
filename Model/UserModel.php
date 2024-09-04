<?php

require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database {
    public function getUsers($limit) {
        $sql = "SELECT * FROM users ORDER BY user_id ASC LIMIT ?";
        $params = [$limit];
        $format = "i";
        return $this->select($sql, $params, $format);
    }

    public function findBy($fields, $params, $format) {
        $sql = "SELECT * FROM users WHERE " . implode(" = ? OR ", $fields) . " = ?";
        return $this->select($sql, $params, $format);
    }

    public function saveUsers($fields, $params, $format) {
        $placeholders = rtrim(str_repeat('?, ', count($fields)), ', ');
        $sql = "INSERT INTO users(".implode(", ", $fields).") VALUES ($placeholders)";
        return $this->query($sql, $params, $format, 'inserted');
    }

    public function updateUsers($fields, $params, $format) {
        $sql = "UPDATE users SET " . implode("= ?, ", $fields) . " = ? WHERE user_id = ?";
        return $this->query($sql, $params, $format, 'updated');
    }

    public function deleteUser($params, $format) {
        $sql = "DELETE FROM users WHERE user_id = ?";
        return $this->query($sql, $params, $format, 'deleted');
    }
}