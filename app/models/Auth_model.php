<?php

class Auth_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function findByUsername($username) {
        $this->db->query("SELECT * FROM automotive_api.user WHERE username = :username");
        $this->db->bind(":username", $username);
        return $this->db->single();
    }
}