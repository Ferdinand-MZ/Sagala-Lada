<?php
// login.php - Versi 1 file penuh dengan Class Auth

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

class Auth {
    private $db;
    private $error = '';

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function attempt($username, $password) {
        if (empty(trim($username)) || empty($password)) {
            $this->error = 'Harap isi semua field!';
            return false;
        }

        $stmt = $this->db->prepare("SELECT id_user, username, password, role FROM user WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            $this->error = 'Username atau password salah!';
            return false;
        }

        $stmt->bind_result($id, $user, $hash, $role);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $id;
            $_SESSION['username']  = $user;
            $_SESSION['role']      = $role;
            $_SESSION['logged_in'] = true;
            header("Location: /server/view/dashboard/dashboard.php");
            exit;
        }

        $this->error = 'Username atau password salah!';
        return false;
    }

    public function getError() {
        return $this->error;
    }
}

// Proses login
$auth = new Auth($koneksi);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->attempt($_POST['username'] ?? '', $_POST['password'] ?? '');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sagala Lada</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:20px;}
        .container{max-width:1200px;width:100%;display:flex;gap:60px;align-items:center;}
        .login-card{background:white;border-radius:12px;padding:50px 40px;box-shadow:0 2px 10px rgba(0,0,0,.1);max-width:480px;width:100%;}
        .login-title{font-size:42px;font-weight:700;margin:8px 0 40px;}
        label{display:block;margin-bottom:8px;font-weight:500;}
        input[type=text],input[type=password]{width:100%;padding:14px 16px;border:1px solid #ddd;border-radius:8px;margin-bottom:20px;font-size:14px;}
        input:focus{outline:none;border-color:#FDB72C;}
        .login-btn{width:100%;padding:16px;background:linear-gradient(135deg,#FDB72C,#F5A623);color:white;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;}
        .login-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(253,183,44,.4);}
        .error{background:#ffe6e6;color:#c53030;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;}
        .illustration img{max-width:100%;height:auto;}
        @media(max-width:968px){.container{flex-direction:column;}.illustration{order:-1;max-width:400px;}}
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div style="font-size:18px;color:#333;">Selamat Datang!</div>
        <h1 class="login-title">Masuk</h1>
        <p style="color:#666;margin-bottom:40px;">Sagala Lada</p>

        <?php if ($auth->getError()): ?>
            <div class="error"><?= htmlspecialchars($auth->getError()) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>

    <div class="illustration">
        <img src="/assets/img/login.png" alt="Login" style="width:700px;height:600px;object-fit:contain;">
    </div>
</div>
</body>
</html>