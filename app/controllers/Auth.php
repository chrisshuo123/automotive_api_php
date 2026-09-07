<?php
class Auth extends Controller {
    public function login() {
        // Kalau sudah login langsung lempar ke car-crud
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/crud');
            exit;
        }
        $this->view('admin/login');
    }

    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $admin = $this->model('Auth_model')->findByUsername($username);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['iduser'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: ' . BASEURL . '/crud');
                exit;
            } else {
                header('Location: ' . BASEURL . '/auth/login?error=1');
                exit;
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
}