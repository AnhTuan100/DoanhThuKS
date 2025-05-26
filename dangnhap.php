<?php
session_start();
include 'ketnoi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        $_SESSION['username'] = $user['username'];
        header("Location: DoanhThu.php");
        exit;
    } else {
        echo "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="styledangnhap.css">
</head>
<body>
    <div class="form-container">
  <h2>Đăng nhập</h2>
  <!-- Hiển thị thông báo lỗi nếu có -->
  <?php if(!empty($message)): ?>
    <div class="error-message"><?= $message ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <div class="form-group">
      <label for="username">Tên đăng nhập</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="password">Mật khẩu</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button class="button" type="submit">Đăng nhập</button>
  </form>
  </div>
</div>
</body>
</html>
