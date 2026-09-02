<?php

class Controller {
    public function view($view, $data = []) {
        require_once '../app/views/' . $view . '.php';
        //return with absolute paths
        // require_once __DIR__ . '/../views/' . $view . '.php';
    }

    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        // return with absolute paths
        // require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model;
    }
}