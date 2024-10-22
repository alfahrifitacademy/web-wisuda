<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - STMIK Bandung</title>
    <link rel="stylesheet" href="../login/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../login/images/logo.png" alt="Logo">
                <h2>Admin</h2>
            </div>
            <form action="../login/login_process.php" method="POST" class="login-form">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="username@gmail.com" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <button type="submit">Masuk</button>
            </form>
        </div>
    </div>
    <footer>
            <p>Copyright 2024 - Pendaftaran Wisuda STMIK Bandung</p>
    </footer>
</body>
</html>
