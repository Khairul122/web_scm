<?php
$restClient = $GLOBALS['restClient'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

function getCurrentUser() {
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

function sendResponse($response) {
    Response::json([
        'success' => $response['success'],
        'data' => $response['data']
    ], $response['http_code']);
}

function methodNotAllowed() {
    Response::json(['error' => 'Method not allowed'], 405);
}

function validateRequired($param, $name) {
    if (empty($param)) {
        Response::json(['error' => $name . ' is required'], 400);
    }
    return $param;
}

$user = getCurrentUser();

if (!$user) {
    Response::json(['error' => 'Authentication required'], 401);
}

if ($user['role'] !== 'admin') {
    Response::json(['error' => 'Admin access required'], 403);
}

try {
    switch ($path) {
        case 'list':
            if ($method !== 'GET') methodNotAllowed();
            $role = Request::get('role');
            $query = $role ? "users?role={$role}" : 'users';
            $response = $restClient->get($query);
            sendResponse($response);
            break;

        case 'create':
            if ($method !== 'POST') methodNotAllowed();
            $input = Request::input();
            if (empty($input['nama_lengkap']) || empty($input['email']) || empty($input['password'])) {
                Response::json(['error' => 'Name, email and password are required'], 400);
            }
            $response = $restClient->post('users', $input);
            sendResponse($response);
            break;

        case 'show':
            if ($method !== 'GET') methodNotAllowed();
            $id = validateRequired(Request::get('id'), 'User ID');
            $response = $restClient->get("users/{$id}");
            sendResponse($response);
            break;

        case 'update':
            if ($method !== 'PUT') methodNotAllowed();
            $id = validateRequired(Request::get('id'), 'User ID');
            $input = Request::input();
            if (empty($input)) {
                Response::json(['error' => 'No data provided'], 400);
            }
            $response = $restClient->put("users/{$id}", $input);
            sendResponse($response);
            break;

        case 'delete':
            if ($method !== 'DELETE') methodNotAllowed();
            $id = validateRequired(Request::get('id'), 'User ID');
            $response = $restClient->delete("users/{$id}");
            sendResponse($response);
            break;

        case 'status':
            if ($method !== 'PATCH') methodNotAllowed();
            $id = validateRequired(Request::get('id'), 'User ID');
            $input = Request::input();
            if (empty($input['status']) || !in_array($input['status'], ['aktif', 'nonaktif'])) {
                Response::json(['error' => 'Valid status (aktif/nonaktif) is required'], 400);
            }
            $response = $restClient->patch("users/{$id}/status", $input);
            sendResponse($response);
            break;

        case 'password':
            if ($method !== 'PATCH') methodNotAllowed();
            $id = validateRequired(Request::get('id'), 'User ID');
            $input = Request::input();
            if (empty($input['password']) || strlen($input['password']) < 6) {
                Response::json(['error' => 'Password must be at least 6 characters'], 400);
            }
            $response = $restClient->patch("users/{$id}/password", $input);
            sendResponse($response);
            break;

        case 'search':
            if ($method !== 'GET') methodNotAllowed();
            $query = validateRequired(Request::get('q'), 'Search query');
            $response = $restClient->get("users/search?q={$query}");
            sendResponse($response);
            break;

        case 'stats':
            if ($method !== 'GET') methodNotAllowed();
            $response = $restClient->get('users/stats');
            sendResponse($response);
            break;

        default:
            if ($method === 'GET') {
                $response = $restClient->get('users');
                sendResponse($response);
            } else {
                Response::json([
                    'error' => 'Endpoint not found',
                    'available_paths' => ['list', 'create', 'show', 'update', 'delete', 'status', 'password', 'search', 'stats']
                ], 404);
            }
            break;
    }
} catch (Exception $e) {
    Response::json(['error' => 'Internal server error'], 500);
}
?>