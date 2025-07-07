<?php
function getCurrentUserFromTokenNavbar() {
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

$user = getCurrentUserFromTokenNavbar();
$userName = $user['nama_lengkap'] ?? 'Guest';
$userEmail = $user['email'] ?? '';
$userRole = ucfirst($user['role'] ?? 'user');

$hour = date('H');
if ($hour < 12) {
    $greeting = 'Selamat Pagi';
} elseif ($hour < 17) {
    $greeting = 'Selamat Siang';
} else {
    $greeting = 'Selamat Malam';
}

error_log('=== NAVBAR TOKEN DEBUG ===');
error_log('User from token: ' . print_r($user ?? 'NOT SET', true));
error_log('User name: ' . $userName);
error_log('User email: ' . $userEmail);
error_log('User role: ' . $userRole);
?>

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="?controller=Dashboard&action=index">
        <img src="<?= View::asset('images/logo.svg') ?>" alt="logo" />
      </a>
      <a class="navbar-brand brand-logo-mini" href="?controller=Dashboard&action=index">
        <img src="<?= View::asset('images/logo-mini.svg') ?>" alt="logo" />
      </a>
    </div>
  </div>
  
  <div class="navbar-menu-wrapper d-flex align-items-top">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text">
          <?php echo $greeting; ?>, <span class="text-black fw-bold"><?php echo htmlspecialchars($userName); ?></span>
        </h1>
      </li>
    </ul>
    
    <ul class="navbar-nav ms-auto">
      <?php if ($user): ?>
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="<?= View::asset('images/faces/face8.jpg') ?>" alt="Profile image" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="<?= View::asset('images/faces/face8.jpg') ?>" alt="Profile image" />
            <p class="mb-1 mt-3 font-weight-semibold"><?php echo htmlspecialchars($userName); ?></p>
            <p class="fw-light text-muted mb-0"><?php echo htmlspecialchars($userEmail); ?></p>
            <p class="fw-light text-muted mb-0 small">Role: <?php echo htmlspecialchars($userRole); ?></p>
          </div>
          <a class="dropdown-item" href="?controller=Auth&action=profile">
            <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
            My Profile
          </a>
          <a class="dropdown-item" href="?controller=Auth&action=logout">
            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
            Sign Out
          </a>
        </div>
      </li>
      <?php else: ?>
      <li class="nav-item">
        <a class="nav-link" href="?controller=Auth&action=index">
          <i class="mdi mdi-login"></i> Login
        </a>
      </li>
      <?php endif; ?>
    </ul>
    
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>

<script>
console.log('=== NAVBAR TOKEN DATA ===');
console.log('User data:', <?= json_encode($user) ?>);
console.log('User Name:', <?= json_encode($userName) ?>);
console.log('User Email:', <?= json_encode($userEmail) ?>);
console.log('User Role:', <?= json_encode($userRole) ?>);
console.log('Token valid:', <?= json_encode($user !== null) ?>);

<?php if ($user): ?>
console.log('Token expires:', new Date(<?= $user['exp'] * 1000 ?>));
console.log('Time until expiry:', Math.floor((<?= $user['exp'] ?> * 1000 - Date.now()) / 1000 / 60), 'minutes');
<?php endif; ?>

function storeTokenInCookie(token) {
    document.cookie = `auth_token=${token}; path=/; max-age=${24*60*60}; SameSite=Lax`;
    console.log('Token stored in cookie');
}

function getTokenFromStorage() {
    return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
}

function checkAndSetToken() {
    const token = getTokenFromStorage();
    if (token && !document.cookie.includes('auth_token=')) {
        storeTokenInCookie(token);
        console.log('Token set from storage to cookie');
    }
}

checkAndSetToken();
</script>