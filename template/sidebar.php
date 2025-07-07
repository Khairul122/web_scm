<?php
require_once 'models/AuthModel.php';
$authModel = new AuthModel();
$role = $authModel->getCurrentRole();
$currentUrl = $_SERVER['REQUEST_URI'];
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item <?php echo (strpos($currentUrl, '/dashboard') !== false || $currentUrl === '/web_scm/' || $currentUrl === '/web_scm') ? 'active' : ''; ?>">
      <a class="nav-link" href="/web_scm/dashboard">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    
    <?php if ($role === 'admin'): ?>
    <li class="nav-item <?php echo (strpos($currentUrl, '/kategori') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="/web_scm/kategori">
        <i class="mdi mdi-tag-multiple menu-icon"></i>
        <span class="menu-title">Kategori</span>
      </a>
    </li>
     <li class="nav-item <?php echo (strpos($currentUrl, '/ekspedisi') !== false) ? 'active' : ''; ?>">
      <a class="nav-link" href="/web_scm/ekspedisi">
        <i class="mdi mdi-truck-delivery menu-icon"></i>
        <span class="menu-title">Ekspedisi</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</nav>