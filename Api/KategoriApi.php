<?php
$restClient = $GLOBALS['restClient'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

error_log('=== KATEGORI API DEBUG ===');
error_log('Method: ' . $method);
error_log('Path: ' . $path);

function getCurrentUser() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$authHeader && isset($_COOKIE['auth_token'])) {
        $authHeader = 'Bearer ' . $_COOKIE['auth_token'];
    }

    error_log('Auth header present: ' . ($authHeader ? 'YES' : 'NO'));

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        error_log('No valid Bearer token found');
        return null;
    }

    $token = $matches[1];
    error_log('Token received: ' . substr($token, 0, 50) . '...');

    $decoded = json_decode(base64_decode($token), true);

    if (!$decoded) {
        error_log('Token decode failed');
        return null;
    }

    if ($decoded['exp'] < time()) {
        error_log('Token expired at: ' . date('Y-m-d H:i:s', $decoded['exp']));
        return null;
    }

    error_log('Token valid for user: ' . $decoded['email'] . ' (role: ' . $decoded['role'] . ')');
    return $decoded;
}

$user = getCurrentUser();

error_log('User data: ' . print_r($user ?? 'NOT SET', true));
error_log('User role: ' . ($user['role'] ?? 'NO ROLE'));

if (!$user) {
    error_log('No valid token found');
    Response::json([
        'error' => 'User not authenticated',
        'message' => 'Valid Bearer token required',
        'debug' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'path' => $path
        ]
    ], 401);
}

if ($user['role'] !== 'admin') {
    error_log('User role is not admin: ' . $user['role']);
    Response::json([
        'error' => 'Admin access required',
        'message' => 'Current role: ' . $user['role'],
        'debug' => [
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'required_role' => 'admin',
            'current_role' => $user['role']
        ]
    ], 403);
}

error_log('Admin access granted for user: ' . $user['nama_lengkap'] . ' (ID: ' . $user['id'] . ')');

switch ($path) {
    case 'list':
        if ($method === 'GET') {
            error_log('Fetching kategori list');
            $response = $restClient->get('kategori');
            error_log('List response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'user' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'create':
        if ($method === 'POST') {
            $input = Request::input();
            error_log('Creating kategori with data: ' . print_r($input, true));
            $response = $restClient->post('kategori', $input);
            error_log('Create response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'input_data' => $input,
                    'api_response' => $response,
                    'created_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'show':
        if ($method === 'GET') {
            $id = Request::get('id');
            error_log('Showing kategori ID: ' . $id);
            $response = $restClient->get("kategori/{$id}");
            error_log('Show response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'kategori_id' => $id,
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'update':
        if ($method === 'PUT') {
            $id = Request::get('id');
            $input = Request::input();
            error_log('Updating kategori ID ' . $id . ' with data: ' . print_r($input, true));
            $response = $restClient->put("kategori/{$id}", $input);
            error_log('Update response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'kategori_id' => $id,
                    'input_data' => $input,
                    'api_response' => $response,
                    'updated_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'delete':
        if ($method === 'DELETE') {
            $id = Request::get('id');
            error_log('Deleting kategori ID: ' . $id);
            $response = $restClient->delete("kategori/{$id}");
            error_log('Delete response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'kategori_id' => $id,
                    'api_response' => $response,
                    'deleted_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'products':
        if ($method === 'GET') {
            $id = Request::get('id');
            error_log('Fetching products for kategori ID: ' . $id);
            $response = $restClient->get("kategori/{$id}/products");
            error_log('Products response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'kategori_id' => $id,
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    default:
        if ($method === 'GET') {
            error_log('Default route - fetching all kategori');
            $response = $restClient->get('kategori');
            error_log('Default response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'route' => 'default',
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        } else {
            error_log('Invalid endpoint or method');
            Response::json([
                'error' => 'Kategori endpoint not found',
                'debug' => [
                    'method' => $method,
                    'path' => $path,
                    'available_paths' => ['list', 'create', 'show', 'update', 'delete', 'products'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], 404);
        }
        break;
}
?>