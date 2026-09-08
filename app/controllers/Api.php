<?php
class Api extends Controller {
    // Insert function khusus User yang mau Kontribusi List Mobil API
    public function insertCar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaFoto = '';

            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/img/';
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $namaFoto = uniqid('car_') . '.' . $ext;
                $targetPath = $uploadDir . $namaFoto;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
            }

            $data = [
                'nama_mobil' => $_POST['nama_mobil'] ?? '',
                'idMerek_fk' => $_POST['idMerek_fk'] ?? null,
                'idJenis_fk' => $_POST['idJenis_fk'] ?? null,
                'horse_power' => $_POST['horse_power'] ?? 0,
                'nama_foto' => $namaFoto,
                'idStatus_fk' => 1  // 'Need Preview'
            ];
            
            $result = $this->model('Home_model')->insertCar($data);

            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            exit;
        }
    }

    // Seluruh get direturn disini semua
    public function getCars() {
        $carList = $this->model('Home_model')->getAllCars() ?: [];
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $carList]);
    }
    public function getMerek() {
        $merekList = $this->model('Home_model')->getAllMerek() ?: [];
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $merekList]);
    }
    public function getJenis() {
        $jenisList = $this->model('Home_model')->getAllJenis() ?: [];
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $jenisList]);
    }
    public function getStatuses() {
        $statusList = $this->model('Home_model')->getAllStatuses() ?: [];
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $statusList]);
    }
}