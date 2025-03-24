<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .min-vh-100 {
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow p-4 text-center" style="max-width: 400px;">
            <div class="mb-4">
                <i class="fas fa-lock text-danger" style="font-size: 4rem;"></i>
                <h1 class="mt-3 fw-bold text-dark">Access Denied</h1>
            </div>
            <p class="text-muted mb-4">
                You do not have the necessary permissions to access this page.
                Please contact your system administrator if you believe this is an error.
            </p>
            <div class="d-flex justify-content-center">
                <a href="../../views/dashboard.php" class="btn btn-primary d-flex align-items-center">
                    <i class="fas fa-home me-2"></i>
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
