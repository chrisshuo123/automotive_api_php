<?php

class Home_model {
    private $db;
    private $carName = 'Yaris';

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllCars() {
        $this->db->query("
            SELECT c.*,
                m.namaMerek AS merek,
                j.namaJenis AS jenis,
                s.namaStatus AS status
            FROM automotive_api.cars c
            LEFT JOIN automotive_api.merek m ON c.idMerek_fk = m.idMerek
            LEFT JOIN automotive_api.jenis j ON c.idJenis_fk = j.idJenis
            LEFT JOIN automotive_api.status s ON c.idStatus_fk = s.idStatus
            ORDER BY c.idCars ASC
        ");
        $result = $this->db->resultSet();
        return $result ?: []; // Kalau null, return array kosong
    }

    public function getAllMerek() {
        return $this->db->all('automotive_api.merek', 'idmerek ASC');
    }
    
    public function insertMerek($data) {
        return $this->db->insert('automotive_api.merek', $data, 'idmerek');
    }

    public function updateMerek($idMerek, $data) {
        return $this->db->update('automotive_api.merek', $data, $idMerek, 'idmerek');
    }

    public function deleteMerek($idMerek) {
        return $this->db->delete('automotive_api.merek', $idMerek, 'idmerek');
    }

    public function getAllJenis() {
        return $this->db->all('automotive_api.jenis', 'idjenis ASC');
    }

    public function getAllStatuses() {
        return $this->db->all('automotive_api.status', 'idstatus ASC');
    }

    public function getCarById($idCars) {
        return $this->db->find('automotive_api.cars', $idCars, 'idcars');
    }

    public function insertCar($data) {
        return $this->db->insert('automotive_api.cars', $data, 'idcars');
    }

    public function updateCar($idCars, $data) {
        return $this->db->update('automotive_api.cars', $data, $idCars, 'idcars');
    }

    public function deleteCar($idCars) {
        return $this->db->delete('automotive_api.cars', $idCars, 'idcars');
    }
}