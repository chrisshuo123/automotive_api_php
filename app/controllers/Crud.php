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
            $id = $_POST['idcars'] ?? 0;
            $data = [
                'nama_mobil' => $_POST['nama_mobil'],
                'idMerek_fk' => $_POST['idMerek_fk'],
                'idJenis_fk' => $_POST['idJenis_fk'],
                'horse_power' => $_POST['horse_power'],
                'nama_foto' => $_POST['nama_foto'],
                'idStatus_fk' => $_POST['idStatus_fk'] ?? 1
            ];

            $result = $this->model('Home_model')->updateCar($data, $idCars);
            echo json_encode(['success' => $result]);
        }
    }

    public function deleteCar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCars = $_POST['idcars'] ?? 0;
            $result = $this->model('Home_model')->deleteCar($idCars);
            // var_dump($result);
            // die();
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
        }
    }
}