<?php
class Home extends Controller {
    public function index() {
        // echo 'home/index';
        echo 'home page';
        $this->view('index');
    }
}