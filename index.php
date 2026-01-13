    <?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'hr')
        header('Location: hr/dashboard.php');
    else
        header('Location: karyawan/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #FF69B4;
            margin-bottom: 15px;
        }
        .form-control:focus {
            border-color: #FF1493;
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            width: 100%;
            color: white;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #FF1493 0%, #FF69B4 100%);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <!-- Placeholder for logo, replace with actual logo image -->
            <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
        </div>
        <h2>Login</h2>
        <form method="POST" action="auth/login.php">
            <input type="email" name="email" class="form-control" placeholder="ID Karyawan" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>