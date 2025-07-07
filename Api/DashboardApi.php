<?php
$restClient = $GLOBALS['restClient'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

switch ($path) {
    case 'stats':
        if ($method === 'GET') {
            if (!isset($_SESSION['user'])) {
                Response::json(['error' => 'Unauthorized'], 401);
            }
            
            $response = $restClient->get('dashboard');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    case 'admin':
        if ($method === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                Response::json(['error' => 'Admin access required'], 403);
            }
            
            $response = $restClient->get('dashboard/admin');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    case 'pengepul':
        if ($method === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pengepul') {
                Response::json(['error' => 'Pengepul access required'], 403);
            }
            
            $response = $restClient->get('dashboard/pengepul');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    case 'roasting':
        if ($method === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'roasting') {
                Response::json(['error' => 'Roasting access required'], 403);
            }
            
            $response = $restClient->get('dashboard/roasting');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    case 'penjual':
        if ($method === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'penjual') {
                Response::json(['error' => 'Penjual access required'], 403);
            }
            
            $response = $restClient->get('dashboard/penjual');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    case 'pembeli':
        if ($method === 'GET') {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pembeli') {
                Response::json(['error' => 'Pembeli access required'], 403);
            }
            
            $response = $restClient->get('dashboard/pembeli');
            Response::json($response['data'], $response['http_code']);
        }
        break;

    default:
        if ($method === 'GET') {
            if (!isset($_SESSION['user'])) {
                Response::json(['error' => 'Unauthorized'], 401);
            }
            
            $response = $restClient->get('dashboard');
            Response::json($response['data'], $response['http_code']);
        } else {
            Response::json(['error' => 'Dashboard endpoint not found'], 404);
        }
        break;
}
?>