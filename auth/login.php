<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sagala Lada</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color:#f5f5f5; display:flex; justify-content:center; align-items:center;
            min-height:100vh; padding:20px;
        }
        .container { display:flex; max-width:1200px; width:100%; gap:60px; align-items:center; }
        .login-card { background:white; border-radius:12px; padding:50px 40px; box-shadow:0 2px 10px rgba(0,0,0,0.1); max-width:480px; width:100%; }
        .welcome-text { font-size:18px; color:#333; margin-bottom:10px; }
        .login-title { font-size:42px; font-weight:700; color:#000; margin-bottom:8px; }
        .subtitle { font-size:14px; color:#666; margin-bottom:40px; }
        .form-group { margin-bottom:24px; }
        label { display:block; font-size:14px; color:#333; margin-bottom:8px; font-weight:500; }
        input[type="text"], input[type="password"] { width:100%; padding:14px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; transition:border-color .3s; }
        input:focus { outline:none; border-color:#FDB72C; }
        .login-btn { width:100%; padding:16px; background:linear-gradient(135deg,#FDB72C,#F5A623); color:white; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; transition:transform .2s,box-shadow .3s; }
        .login-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(253,183,44,.4); }
        .error { background:#ffe6e6; color:#c53030; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-size:14px; }
        .illustration img { max-width:100%; height:auto; }
        @media (max-width:968px) { .container{flex-direction:column;} .illustration{order:-1; max-width:400px;} }
        @media (max-width:480px) { .login-card{padding:40px 30px;} .login-title{font-size:36px;} }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="welcome-text">Selamat Datang!</div>
        <h1 class="login-title">Masuk</h1>
        <p class="subtitle">Sagala Lada</p>

        <?php
        session_start();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username !== '' && $password !== '') {
                $stmt = $koneksi->prepare("SELECT id_user, username, password, role FROM user WHERE username = ? LIMIT 1");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($id, $user, $hash, $role);
                    $stmt->fetch();

                    if (password_verify($password, $hash)) {
                        $_SESSION['user_id']   = $id;
                        $_SESSION['username']  = $user;
                        $_SESSION['role']      = $role;
                        header("Location: /server/view/dashboard/dashboard.php");
                        exit;
                    } else $error = 'Username atau password salah!';
                } else $error = 'Username atau password salah!';
                $stmt->close();
            } else $error = 'Harap isi semua field!';
        }

        if ($error) echo "<div class='error'>$error</div>";
        ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" required>
                    <span class="toggle-password" onclick="togglePassword()">Show</span>
                </div>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>

    <div class="illustration">
        <img src="/assets/img/login.png" alt="Login" style="width:700px;height:600px;object-fit:contain;">
    </div>
</div>

<script>
function togglePassword() {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>