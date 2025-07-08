<?php
$restClient = $GLOBALS['restClient'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

error_log('=== EKSPEDISI API DEBUG ===');
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
            error_log('Fetching ekspedisi list');
            $response = $restClient->get('kurir');
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
            error_log('Creating ekspedisi with data: ' . print_r($input, true));
            $response = $restClient->post('kurir', $input);
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
            error_log('Showing ekspedisi ID: ' . $id);
            $response = $restClient->get("kurir?id={$id}");
            error_log('Show response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'ekspedisi_id' => $id,
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
            $input['id'] = $id;
            error_log('Updating ekspedisi ID ' . $id . ' with data: ' . print_r($input, true));
            $response = $restClient->put("kurir", $input);
            error_log('Update response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'ekspedisi_id' => $id,
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
            error_log('Deleting ekspedisi ID: ' . $id);
            $response = $restClient->delete("kurir", ['id' => $id]);
            error_log('Delete response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'ekspedisi_id' => $id,
                    'api_response' => $response,
                    'deleted_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'status':
        if ($method === 'PATCH') {
            $id = Request::get('id');
            $input = Request::input();
            $input['id'] = $id;
            error_log('Updating ekspedisi status ID ' . $id . ' with data: ' . print_r($input, true));
            $response = $restClient->patch("kurir", $input);
            error_log('Status update response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'ekspedisi_id' => $id,
                    'input_data' => $input,
                    'api_response' => $response,
                    'updated_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'available-codes':
        if ($method === 'GET') {
            error_log('Fetching available courier codes');
            $response = $restClient->get('kurir/available-codes');
            error_log('Available codes response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'stats':
        if ($method === 'GET') {
            error_log('Fetching ekspedisi stats');
            $response = $restClient->get('kurir/stats');
            error_log('Stats response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'search':
        if ($method === 'GET') {
            $query = Request::get('q');
            error_log('Searching ekspedisi with query: ' . $query);
            $response = $restClient->get("kurir/search?q={$query}");
            error_log('Search response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'search_query' => $query,
                    'api_response' => $response,
                    'requested_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'import':
        if ($method === 'POST') {
            error_log('Importing ekspedisi from API');
            $response = $restClient->post('kurir/import', []);
            error_log('Import response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'imported_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'bulk-update':
        if ($method === 'POST') {
            $input = Request::input();
            error_log('Bulk updating ekspedisi status with data: ' . print_r($input, true));
            $response = $restClient->post('kurir/bulk-update', $input);
            error_log('Bulk update response: ' . print_r($response, true));
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'input_data' => $input,
                    'api_response' => $response,
                    'updated_by' => $user['email'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    default:
        if ($method === 'GET') {
            error_log('Default route - fetching all ekspedisi');
            $response = $restClient->get('kurir');
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
                'error' => 'Ekspedisi endpoint not found',
                'debug' => [
                    'method' => $method,
                    'path' => $path,
                    'available_paths' => ['list', 'create', 'show', 'update', 'delete', 'status', 'available-codes', 'stats', 'search', 'import', 'bulk-update'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], 404);
        }
        break;
}
?>