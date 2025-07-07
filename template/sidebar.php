<?php
function getCurrentUserFromToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$authHeader && isset($_COOKIE['auth_token'])) {
        $authHeader = 'Bearer ' . $_COOKIE['auth_token'];
    }

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return null;
    }

    $token = $matches[1];
    $decoded = json_decode(base64_decode($token), true);

    if (!$decoded || $decoded['exp'] < time()) {
        return null;
    }

    return $decoded;
}

$user = getCurrentUserFromToken();
$role = $user['role'] ?? '';
$userName = $user['nama_lengkap'] ?? '';
$userEmail = $user['email'] ?? '';
$currentController = $_GET['controller'] ?? 'Dashboard';

function isActive($controller) {
    global $currentController;
    return strtolower($currentController) === strtolower($controller) ? 'active' : '';
}

error_log('=== SIDEBAR TOKEN DEBUG ===');
error_log('User from token: ' . print_r($user ?? 'NOT SET', true));
error_log('Role: ' . $role);
error_log('Current controller: ' . $currentController);
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item <?= isActive('Dashboard') ?>">
      <a class="nav-link" href="?controller=Dashboard&action=index">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    
    <?php if ($role === 'admin'): ?>
    <li class="nav-item <?= isActive('Kategori') ?>">
      <a class="nav-link" href="?controller=Kategori&action=index">
        <i class="mdi mdi-tag-multiple menu-icon"></i>
        <span class="menu-title">Kategori</span>
      </a>
    </li>
    <?php endif; ?>
    
    <li class="nav-item <?= isActive('Auth') ?>">
      <a class="nav-link" href="?controller=Auth&action=profile">
        <i class="mdi mdi-account-circle menu-icon"></i>
        <span class="menu-title">Profil</span>
      </a>
    </li>
  </ul>
</nav>

<script>
console.log('=== SIDEBAR TOKEN INFO ===');
console.log('User data:', <?= json_encode($user) ?>);
console.log('Role:', <?= json_encode($role) ?>);
console.log('User name:', <?= json_encode($userName) ?>);
console.log('User email:', <?= json_encode($userEmail) ?>);
console.log('Controller:', <?= json_encode($currentController) ?>);
console.log('Token valid:', <?= json_encode($user !== null) ?>);

<?php if ($user): ?>
console.log('Token expires:', new Date(<?= $user['exp'] * 1000 ?>));
console.log('Time until expiry:', Math.floor((<?= $user['exp'] ?> * 1000 - Date.now()) / 1000 / 60), 'minutes');
<?php endif; ?>

function checkTokenExpiry() {
    <?php if ($user): ?>
    const expiry = <?= $user['exp'] ?> * 1000;
    const now = Date.now();
    const timeLeft = expiry - now;
    
    if (timeLeft <= 0) {
        console.warn('Token expired, redirecting to login...');
        window.location.href = '?controller=Auth&action=index';
    } else if (timeLeft <= 5 * 60 * 1000) {
        console.warn('Token expires in', Math.floor(timeLeft / 1000 / 60), 'minutes');
    }
    <?php else: ?>
    console.warn('No valid token found');
    <?php endif; ?>
}

setInterval(checkTokenExpiry, 60000);
checkTokenExpiry();
</script>