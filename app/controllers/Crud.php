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
            $data = [
                'nama_mobil' => $_POST['nama_mobil'] ?? '',
                'idMerek_fk' => $_POST['idMerek_fk'] ?? null,
                'idJenis_fk' => $_POST['idJenis_fk'] ?? null,
                'horse_power' => $_POST['horse_power'] ?? 0,
                'nama_foto' => $_POST['nama_foto'] ?? '',
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