<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();  // Buat fitur login admin
}

require_once __DIR__ . "/config/config.php";

require_once __DIR__ . "/core/App.php";
require_once __DIR__ . "/core/Controller.php";
require_once __DIR__ . "/core/Database.php";
// require_once __DIR__ . "/core/Flasher.php";