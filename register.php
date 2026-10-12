<?php
session_start();
// Wajib menyertakan koneksi database
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = 'Semua bidang wajib diisi!';
    } else {
        // 1. Cek apakah email sudah terdaftar di database
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email sudah terdaftar, tolong login';
        } else {
            // 2. Simpan user baru ke database
            $insertStmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'user')");
            $insertStmt->bind_param("sss", $fullname, $email, $password);

            if ($insertStmt->execute()) {
                $success = 'Pendaftaran berhasil! Silakan login.';
            } else {
                $error = 'Gagal mendaftar, silakan coba lagi.';
            }
            $insertStmt->close();
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
  <title>Register — ChargeGuard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-container { max-width: 400px; margin: 80px auto; background: var(--surface); padding: 30px; border-radius: 18px; border: 1px solid var(--line); box-shadow: var(--shadow); }
    .auth-container h2 { margin-top: 0; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted); }
    .form-group input { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid var(--line); font: inherit; }
    .error-msg { background: #fff0f0; color: var(--danger); padding: 10px; border-radius: 10px; margin-bottom: 15px; font-size: 13px; }
    .success-msg { background: #e9f7ef; color: var(--accent); padding: 10px; border-radius: 10px; margin-bottom: 15px; font-size: 13px; }
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
    <h2>Buat Akun Baru</h2>

    <?php if ($error): ?>
      <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <div class="form-group">
        <label for="fullname">Nama Lengkap</label>
        <input type="text" id="fullname" name="fullname" placeholder="Nama Anda" required>
      </div>
      <div class="form-group">
        <label for="email">Email Kampus</label>
        <input type="email" id="email" name="email" placeholder="nama@kampus.ac.id" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn" style="width: 100%;">Daftar</button>
    </form>

    <div class="auth-footer">
      Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
  </div>
</body>
</html>