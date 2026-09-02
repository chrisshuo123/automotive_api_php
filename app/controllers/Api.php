<?php
class Api extends Controller {
    public function cars() {
        header('Content-Type: application/json');
        $model = $this->model('Home_model');
        $cars = $model->getAllCars();
        echo json_encode(['data' => $cars]);
    }
}