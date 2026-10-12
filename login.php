<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi!';
    } else {
        // Cek pengguna berdasarkan email di database
        $stmt = $conn->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Cocokkan password
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit();
            } else {
                $error = 'Email/Password salah';
            }
        } else {
            $error = 'Email/Password salah';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — ChargeGuard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-container { max-width: 400px; margin: 80px auto; background: var(--surface); padding: 30px; border-radius: 18px; border: 1px solid var(--line); box-shadow: var(--shadow); }
    .auth-container h2 { margin-top: 0; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted); }
    .form-group input { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid var(--line); font: inherit; }
    .error-msg { background: #fff0f0; color: var(--danger); padding: 10px; border-radius: 10px; margin-bottom: 15px; font-size: 13px; }
    .auth-footer { margin-top: 15px; text-align: center; font-size: 13px; color: var(--muted); }
    .auth-footer a { color: var(--accent); text-decoration: none; font-weight: bold; }
  </style>
</head>
<body>
  <div class="auth-container">
    <div class="brand" style="margin-bottom: 20px;">
      <div class="logo">⚡</div>
      <div>ChargeGuard</div>
    </div>
    <h2>Masuk ke Akun</h2>
    
    <?php if ($error): ?>
      <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="nama@kampus.ac.id" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
  </div>
</body>
</html>