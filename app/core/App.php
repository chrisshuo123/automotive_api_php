<?php

class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this -> parseURL();
        // vardump($url);
        // die();

        // Controller
        if($url && file_exists('../app/controllers/' . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
            //var_dump($url); // <-- bisa dicommand.  var_dump utk permudah kita membaca direktori 'controller/method/params/params/...'
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller; //skrg classnya di-instansiasi, spy kita bisa memanggil methodnya nanti
    
        // Method
        if(isset($url[1])) {
            if(method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Params
        if(!empty($url)) {
            $this->params = array_values($url);
            var_dump($url); // Dicomment dulu jika tidak memerlukan pengecekan halaman.  var_dump utk permudah kita membaca direktori 'controller/method/params/params/...'
        }

        // Jalankan controller & method, serta kirimkan params jika ada
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // Sesi belajar Part #3 Routing
    //public function __construct() {
        //var_dump($_GET);
        //$url = $this -> parseURL();
        //var_dump($url);
    //}

    public function parseURL() {

        if(isset($_GET['url']) ) {
            $url = rtrim($_GET['url'], '/'); // rtrim agar slash '/' dibagian paling depannya URL bisa ngilang saat ditekan enter
            $url = filter_var($url, FILTER_SANITIZE_URL); // Membersihkan URL dari karakter2 yang ngacau utk menghindar hacker
            $url = explode('/', $url); // Dibagian ini, URL nya kita pecah berdasarkan tanda slash '/' menggunakan fungsi 'explode'.  awalnya '$url = explode(delimiter, string)', yang delimiter itu slash '/', jadi slash-slashnya ilang, jadi string2 nya berubah menjadi elemen array, dari yang mana? dari '$url'. 
            // array => key itu yang kita butuhkan utk nanti kita jalankan controller dan method kita.  Sampai sini, kita sudah berhasil melakukan routing dan indeks kita.
            
             // Debug dengan format rapi
            //echo "<pre>";
            //echo "array (size=" . count($url) . ")\n";
            //foreach ($url as $key => $value) {
            //    echo "  $key => string '$value' (length=" . strlen($value) . ")\n";
            //}
            //echo "</pre>";
            
            return $url;
        }

        // Return empty array if no URL is provided
        return [];
    }
}