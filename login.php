<?php
include 'config/koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user berdasarkan username
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // LANGSUNG COCOKAN TEKS BIASA (Tanpa Hash)
        if ($password === $row['password']) {
            $_SESSION['login'] = true;
            $_SESSION['nama'] = $row['nama_lengkap'];
            header("Location: index.php");
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - SAR GARAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white d-flex align-items-center" style="height: 100vh;">
    <div class="container card bg-secondary p-4" style="max-width: 400px;">
        <h3 class="text-center mb-3">SAR GARAGE LOGIN</h3>
        <?php if(isset($error)) : ?>
            <div class="alert alert-danger p-2 text-center">Username/Password Salah!</div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-warning w-100">Masuk</button>
        </form>
    </div>
</body>
</html>
