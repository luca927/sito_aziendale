<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['username'])) {
    $user = [
        'username' => $_SESSION['username'],
        'id' => $_SESSION['user_id']
    ];
} else {
    $user = null;
}
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Arsnet</title>

  <!-- Google Font + Bootstrap + FontAwesome -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css?v=2" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/img/logo_delta.png" alt="logo" style="height:34px;margin-right:10px">
      <span class="fw-semibold text-primary">Arsnet</span>
    </a>

  <!--  <form class="d-flex ms-auto me-3" onsubmit="return false;">
      <input id="search" class="form-control form-control-sm rounded-pill" type="search" placeholder="Cerca..." aria-label="Search">
    </form> -->

    <ul class="navbar-nav ms-auto align-items-center">
      <?php if ($user): ?>
        <li class="nav-item me-3 text-secondary">Ciao, <strong><?= htmlspecialchars($user['username']) ?></strong></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-secondary" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-regular fa-user"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profilo.php">Profilo</a></li>
            <li>
              <a class="dropdown-item text-danger" href="logout.php">
              <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
            </li>
          </ul>
        </li>
      <?php else: ?>
        <li class="nav-item"><a class="btn btn-primary btn-sm rounded-pill" href="login.php">Accedi</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>


