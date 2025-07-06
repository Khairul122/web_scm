<?php
define('PANEL_NAME', 'SCM Web Panel');
define('PANEL_VERSION', '1.0.0');

// Sesuaikan dengan backend API Anda yang sebenarnya
define('API_BASE_URL', 'http://localhost/backend_scm/api'); 
define('PANEL_BASE_URL', 'http://localhost/web_scm');

define('ASSETS_URL', PANEL_BASE_URL . '/assets');
define('VIEWS_PATH', __DIR__ . '/views/');
define('CONTROLLERS_PATH', __DIR__ . '/controllers/');

define('SESSION_NAME', 'scm_panel_session');
define('SESSION_LIFETIME', 3600);

date_default_timezone_set('Asia/Jakarta');

ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}