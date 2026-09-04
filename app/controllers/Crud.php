<?php
class Crud extends Controller {
    public function index() {
        $data['judul'] = "Car CRUD Panel";
        $data['carList'] = $this->model('Home_model')->getAllCars() ?: []; // Pastikan array kosong kalau null
        $data['merekList'] = $this->model('Home_model')->getAllMerek() ?: []; // Pastikan array kosong kalau null
        $data['jenisList'] = $this->model('Home_model')->getAllJenis() ?: []; // Pastikan array kosong kalau null
        echo 'car-crud page';
        $this->view('car-crud', $data);
    }

    public function test() {
        echo "Test method works!";
        // console.log("Test method works!");
    }

    public function getCars() {
        header('Content-Type: application/json');
        $cars = $this->model('Home_model')->getAllCars();
        echo json_encode(['success' => true, 'data' => $cars]);
        exit;
    }

    public function insertCar() {
        // echo "insert car berhasil ditampilkan";
        // die(); // Hentikan eksekusi
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Untuk upload files/photo:
            $namaFoto = '';

            // Handle file upload kalau ada
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/img/';  // sesuaikan path relatif dari lokasi controller
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $namaFoto = uniqid('car_') . '.' . $ext; // Nama file unik, hindari bentrok nama sama
                $targetPath = $uploadDir . $namaFoto;

                if(!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Gagal Upload Foto']);
                    exit;
                }
            };

            $data = [
                'nama_mobil' => $_POST['nama_mobil'] ?? '',
                'idMerek_fk' => $_POST['idMerek_fk'] ?? null,
                'idJenis_fk' => $_POST['idJenis_fk'] ?? null,
                'horse_power' => $_POST['horse_power'] ?? 0,
                'nama_foto' => $namaFoto,
                'idStatus_fk' => $_POST['idStatus_fk'] ?? 1
            ];

            $result = $this->model('Home_model')->insertCar($data);

            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$result, 'id' => $result]);
            exit;
        }
    }

    public function updateCar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCars = $_POST['idcars'] ?? 0;

            // Ambil dulu data mobil lama, buat tahu nama_foto sebelumnya
            $car = $this->model('Home_model')->getCarById($idCars);
            $namaFoto = $car['nama_foto'] ?? ''; // Default: pertahankan foto lama

            // Kalau user upload foto baru, proses upload dan timpa $namaFoto
            if($isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/img/';
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $namaFotoBaru = uniqid('car_') . '.' . $ext;
                $targetPath = $uploadDir . $namaFotoBaru;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $namaFoto = $namaFotoBaru;  // Upload sukses, pakai nama file baru
                }
            }

            $data = [
                'nama_mobil' => $_POST['nama_mobil'] ?? '',
                'idMerek_fk' => $_POST['idMerek_fk'] ?? NULL,
                'idJenis_fk' => $_POST['idJenis_fk'] ?? NULL,
                'horse_power' => $_POST['horse_power'] ?? 0,
                'nama_foto' => $namaFoto,
                'idStatus_fk' => $_POST['idStatus_fk'] ?? 1
            ];

            $result = $this->model('Home_model')->updateCar($idCars, $data);

            // Kalau update DB sukses, ADA foto lama, DAN foto baru beda dari foto lama -> hapus foto lama
            if($result && $car && !empty($car['nama_foto']) && $car['nama_foto'] !== $namaFoto) {
                $oldFilePath = __DIR__ . '/../../public/img/' . $car['nama_foto'];
                if(file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$result]);
        }
    }

    public function deleteCar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCars = $_POST['idcars'] ?? 0;
            // Ambil dulu data mobilnya, buat menangkap nama_foto
            $car = $this->model('Home_model')->getCarById($idCars);

            // Hapus row dari database
            $result = $this->model('Home_model')->deleteCar($idCars);
            
            // Kalau delete DB sukses DAN ada nama_foto, hapus file fisiknya
            if($result && $car && !empty($car['nama_foto'])) {
                $filePath = __DIR__ . '/../../public/img/' . $car['nama_foto'];
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
        }
    }

    public function getMerek() {   // Merek (IND) <-> Brand (ENG)
        header('Content-Type: application/json');
        $idBrand = $this->model('Home_model')->getAllMerek();
        echo json_encode(['success' => true, 'data' => $idBrand]);
        exit;
    }

    public function addMerek() {  // Merek (IND) <-> Brand (ENG)
        $namaMerek = $_POST['namamerek'] ?? '';
        $addMerek = $this->model('Home_model')->insertMerek(['namamerek' => $namaMerek]) ?: false;

        header('Content-Type: application/json');
        if($addMerek) {
            echo json_encode(['success' => true, 'data' => $addMerek]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function editMerek() {  // Merek (IND) <-> Brand (ENG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMerek = $_POST['idmerek'] ?? 0;
            $namaMerek = $_POST['namamerek'] ?? "";

            $result = $this->model('Home_model')->updateMerek($idMerek, ['namamerek' => $namaMerek]);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
        }
    }

    public function deleteMerek() {  // Merek (IND) <-> Brand (ENG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMerek = $_POST['idmerek'] ?? 0;
            
            // Hapus row dari database
            $result = $this->model('Home_model')->deleteMerek($idMerek);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
        }
    }

    public function getJenis() {  // Jenis (IND) <-> Type (ENG)
        header('Content-Type: application/json');
        $cars = $this->model('Home_model')->getAllJenis();
        echo json_encode(['success' => true, 'data' => $cars]);
        exit;
    }

    public function getStatuses() {
        header('Content-Type: application/json');
        $cars = $this->model('Home_model')->getAllStatuses();
        echo json_encode(['success' => true, 'data' => $cars]);
        exit;
    }
}