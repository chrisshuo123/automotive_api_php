<?php
class Home extends Controller {
    public function index() {
        // echo 'home/index';
        echo 'home page';
        $this->view('user/index');
        // For the models already being called via Api::getCars() method globally to script.js
    }

    public function insert() {
        $data['judul'] = "Insert Car User Panel";
        $data['carList'] = $this->model('Home_model')->getAllCars() ?: []; // Pastikan array kosong kalau null
        $data['merekList'] = $this->model('Home_model')->getAllMerek() ?: []; // Pastikan array kosong kalau null
        $data['jenisList'] = $this->model('Home_model')->getAllJenis() ?: []; // Pastikan array kosong kalau null
        echo 'insert page (user panel)';
        $this->view('user/insert', $data);
    }
}