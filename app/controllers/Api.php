<?php
require_once __DIR__ . '/../config/config.php';

class Api extends Controller {
    // Insert function khusus User yang mau Kontribusi List Mobil API
    public function insertCar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaFoto = '';
            $uploadSuccess = false;

            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/img/';
                // Create directory if $uploadDir doesn't exits
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Failed creating upload directory']);
                        exit;
                    }
                }

                // Validate file extension (optional but recommended)
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'tiff', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if(!in_array($ext, $allowed)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
                    exit;
                }

                $namaFoto = uniqid('car_') . '.' . $ext;
                $targetPath = $uploadDir . $namaFoto;

                // Move file with error checking
                if(move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $uploadSuccess = true;
                } else {
                    // Failed handle error
                    // $namaFoto = ''; // Reset
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Fail to upload photo']);
                    exit;
                }
            } else {
                // No file uploaded - decide if this is an error or acceptable
                // If image is optional, continue without exiting
                // If image is required, exit with error
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No uploaded files']);
                exit;
            }

            // Prepare the data if only we have proceed here (upload success)
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
            echo json_encode([
                'success' => (bool)$result,
                'id' => $result,
                'upload_success' => $uploadSuccess,
                'filename' => $namaFoto
            ]);
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