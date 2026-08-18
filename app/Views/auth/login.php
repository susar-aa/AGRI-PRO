<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agri Co-Op ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom ERP CSS -->
    <link href="<?= \Core\Helper::assetUrl('css/custom.css'); ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #081c15 0%, #1b4332 50%, #2d6a4f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #f4f7f5;
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
        }
        .login-body {
            padding: 2rem;
        }
        .app-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1b4332;
            color: #74c69d;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="app-badge">
            <i class="bi bi-tree-fill"></i> AGRI CO-OP ERP
        </div>
        <h5 class="font-weight-bold mb-1 text-dark">සීමා සහිත ඇග්රි කෝප් සමූපකාර සමිතිය</h5>
        <small class="text-muted d-block mb-1">Agri Co-Op Cooperative Society Limited</small>
        <small class="text-secondary fw-semibold">Reg No: කෑ/1027 (KE/1027)</small>
    </div>

    <div class="login-body">
        <?php if ($flashError = \Core\Session::getFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess = \Core\Session::getFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($flashInfo = \Core\Session::getFlash('info')): ?>
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> <?= htmlspecialchars($flashInfo, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= \Core\Helper::baseUrl('login'); ?>" method="POST">
            <?= \Core\CSRF::getFormField(); ?>

            <div class="mb-3">
                <label for="username" class="form-label font-weight-medium">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Enter username" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label font-weight-medium">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 font-weight-bold shadow-sm" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to ERP
            </button>
        </form>


    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
