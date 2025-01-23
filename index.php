<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="index.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Lora:wght@400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="left-section">
      <h1>Selamat Datang di Reza Jaya Bangunan</h1>
      <p>Kelola inventaris Anda secara efisien dengan aplikasi saya yang mudah digunakan.
         Lacak stok Anda, pantau barang yang masuk dan keluar, dan pertahankan catatan yang akurat dengan mudah.</p>
    </div>
    <div class="right-section">
        <div class="login-box">
            <h2>Login</h2>
            <form class="login-form" action="proses_login.php" method="POST">
              <div class="input-group">
                <input type="text" id="username" name="username" required>
                <label for="username">Username</label>
              </div>
              <div class="input-group">
                <input type="password" id="password" name="password" required>
                <label for="password">Password</label>
              </div>
              <button type="submit">Login</button>
            </form>
          </div>
          
    </div>
  </div>
</body>
</html>
