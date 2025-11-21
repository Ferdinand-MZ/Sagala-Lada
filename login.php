<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Masuk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            gap: 60px;
            align-items: center;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 480px;
        }

        .welcome-text {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .login-title {
            font-size: 42px;
            font-weight: 700;
            color: #000;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #FDB72C;
        }

        input::placeholder {
            color: #ccc;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .forgot-password {
            font-size: 14px;
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #FDB72C;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #FDB72C 0%, #F5A623 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(253, 183, 44, 0.4);
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #999;
        }

        .register-link a {
            color: #000;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #FDB72C;
        }

        .illustration {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .illustration img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
            }

            .illustration {
                order: -1;
                max-width: 400px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 30px;
            }

            .login-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="welcome-text">Selamat Datang!</div>
            <h1 class="login-title">Masuk</h1>
            <p class="subtitle">Lorem Ipsum is simply</p>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your user name" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="form-footer">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember Me</span>
                    </label>
                    <a href="#" class="forgot-password">Forgot Password ?</a>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <div class="register-link">
                    Don'y have an Account ? <a href="register.php">Register</a>
                </div>
            </form>
        </div>

        <div class="illustration">
            <svg width="500" height="400" viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Person 1 (left) -->
                <path d="M180 120C180 120 190 100 210 100C230 100 240 120 240 120C240 120 250 130 250 150L240 180L220 200L200 180L190 150C190 130 180 120 180 120Z" fill="#000"/>
                <ellipse cx="215" cy="220" rx="40" ry="50" fill="#fff" stroke="#000" stroke-width="2"/>
                <path d="M195 240L180 280L170 320" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <path d="M235 240L250 280L260 320" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <path d="M190 270L180 290L160 300" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <path d="M240 270L260 290L280 300" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <rect x="230" y="260" width="40" height="50" rx="5" fill="#000"/>
                <rect x="235" y="265" width="30" height="35" fill="#fff"/>
                <line x1="240" y1="275" x2="260" y2="275" stroke="#000" stroke-width="1"/>
                <line x1="240" y1="280" x2="260" y2="280" stroke="#000" stroke-width="1"/>
                <line x1="240" y1="285" x2="260" y2="285" stroke="#000" stroke-width="1"/>
                <!-- Coffee cup -->
                <rect x="185" y="305" width="20" height="25" rx="2" fill="#000"/>
                <ellipse cx="195" cy="305" rx="12" ry="4" fill="#FDB72C"/>
                
                <!-- Speech bubble -->
                <ellipse cx="260" cy="140" rx="35" ry="30" fill="none" stroke="#000" stroke-width="2"/>
                <path d="M245 155L240 165L250 160" fill="#fff" stroke="#000" stroke-width="2"/>

                <!-- Person 2 (right) -->
                <path d="M350 160C350 160 340 180 340 200L350 230L370 230L380 200C380 180 370 160 350 160Z" fill="#fff" stroke="#000" stroke-width="2"/>
                <ellipse cx="365" cy="150" rx="25" ry="30" fill="#fff" stroke="#000" stroke-width="2"/>
                <circle cx="365" cy="145" r="3" fill="#000"/>
                <path d="M355 155C355 155 365 160 375 155" stroke="#000" stroke-width="1" fill="none"/>
                <path d="M340 230L330 270L320 310" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <path d="M390 230L400 270L410 310" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <ellipse cx="365" cy="260" rx="50" ry="55" fill="#000"/>
                <path d="M340 250L315 270L310 285" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <path d="M390 250L415 270L420 285" stroke="#000" stroke-width="2" stroke-linecap="round"/>
                <!-- Jacket details -->
                <rect x="360" y="240" width="10" height="40" fill="#fff" rx="1"/>
                <circle cx="365" cy="250" r="2" fill="#000"/>
                <circle cx="365" cy="260" r="2" fill="#000"/>
                <circle cx="365" cy="270" r="2" fill="#000"/>
                
                <!-- Hair -->
                <path d="M345 140C345 140 355 125 365 125C375 125 385 140 385 140C385 140 390 145 390 155" fill="#000"/>
                
                <!-- Speech bubble 2 -->
                <ellipse cx="300" cy="180" rx="40" ry="35" fill="none" stroke="#000" stroke-width="2"/>
                <path d="M320 205L330 220L315 210" fill="#fff" stroke="#000" stroke-width="2"/>
            </svg>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html> 