<?php
session_start();
require_once __DIR__ . '/controller/authentication/authentication.php';
require_once 'database/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Authentication();
    if ($auth->login($_POST['username'], $_POST['password'])) {
        header("Location: pages/views/dashboard.php");
        exit();
    } else {
        $_SESSION['toast'] = [
            'message' => 'Invalid username or password',
            'type' => 'error'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-lg mx-3" style="width: 900px; max-height: 500px;">
            <div class="row g-0">
                <!-- Left side - Image/Logo -->
                <div class="col-md-6 d-none d-md-block">
                    <div class="h-100 d-flex align-items-center justify-content-center">
                        <img src="assets\images\illustrations\hero_image_2.svg" alt="Logo"
                            style="width: 250px; height: 300px; object-fit: contain;">
                    </div>
                </div>

                <!-- Right side - Login Form -->
                <div class="col-md-6">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Please sign in to your account</p>
                        </div>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <?php
                                if (isset($_SESSION['toast'])) {
                                    echo '<div class="alert alert-danger">' . $_SESSION['toast']['message'] . '</div>';
                                    unset($_SESSION['toast']);
                                }
                                ?>
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter your username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your password">
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
