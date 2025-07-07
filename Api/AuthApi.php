<?php
$restClient = $GLOBALS['restClient'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

error_log('=== AUTH API DEBUG ===');
error_log('Method: ' . $method);
error_log('Path: ' . $path);

switch ($path) {
    case 'login':
        if ($method === 'POST') {
            $input = Request::input();
            error_log('Login attempt with data: ' . print_r($input, true));
            
            $response = $restClient->post('auth/login', $input);
            error_log('Login response: ' . print_r($response, true));
            
            if ($response['success'] && isset($response['data']['token'])) {
                $token = $response['data']['token'];
                $user = $response['data']['user'];
                
                error_log('Login successful, setting token cookie for user: ' . $user['email']);
                
                TokenManager::setTokenCookie($token);
                
                $responseData = [
                    'success' => true,
                    'message' => $response['data']['message'],
                    'token' => $token,
                    'user' => $user,
                    'debug' => [
                        'token_set' => true,
                        'cookie_set' => true,
                        'user_authenticated' => true,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
                
                Response::json($responseData, $response['http_code']);
            } else {
                error_log('Login failed: ' . ($response['data']['error'] ?? 'Unknown error'));
                Response::json([
                    'success' => false,
                    'error' => $response['data']['error'] ?? 'Login failed',
                    'debug' => [
                        'api_response' => $response,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], $response['http_code']);
            }
        }
        break;

    case 'register':
        if ($method === 'POST') {
            $input = Request::input();
            error_log('Register attempt with data: ' . print_r($input, true));
            
            $response = $restClient->post('auth/register', $input);
            error_log('Register response: ' . print_r($response, true));
            
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            $user = TokenManager::getCurrentUser();
            error_log('Logout request from user: ' . ($user['email'] ?? 'NO TOKEN'));
            
            $response = $restClient->post('auth/logout');
            error_log('Logout response: ' . print_r($response, true));
            
            TokenManager::clearTokenCookie();
            error_log('Token cookie cleared');
            
            Response::json([
                'success' => true,
                'message' => 'Logged out successfully',
                'debug' => [
                    'token_cleared' => true,
                    'cookie_cleared' => true,
                    'api_response' => $response,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], 200);
        }
        break;

    case 'profile':
        if ($method === 'GET') {
            $user = TokenManager::getCurrentUser();
            
            if (!$user) {
                error_log('Profile request without valid token');
                Response::json([
                    'error' => 'Unauthorized',
                    'message' => 'Valid Bearer token required',
                    'debug' => [
                        'token_valid' => false,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], 401);
            }
            
            error_log('Profile request from user: ' . $user['email']);
            
            $response = $restClient->get('auth/profile');
            error_log('Profile response: ' . print_r($response, true));
            
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'user' => $user['email'],
                    'api_response' => $response,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        } 
        elseif ($method === 'PUT') {
            $user = TokenManager::getCurrentUser();
            
            if (!$user) {
                error_log('Profile update without valid token');
                Response::json([
                    'error' => 'Unauthorized',
                    'message' => 'Valid Bearer token required',
                    'debug' => [
                        'token_valid' => false,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], 401);
            }
            
            $input = Request::input();
            error_log('Profile update from user: ' . $user['email'] . ' with data: ' . print_r($input, true));
            
            $response = $restClient->put('auth/profile', $input);
            error_log('Profile update response: ' . print_r($response, true));
            
            if ($response['success'] && isset($response['data']['token'])) {
                $newToken = $response['data']['token'];
                TokenManager::setTokenCookie($newToken);
                error_log('New token set after profile update');
                
                Response::json([
                    'success' => true,
                    'data' => $response['data'],
                    'debug' => [
                        'token_updated' => true,
                        'api_response' => $response,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], $response['http_code']);
            } else {
                Response::json([
                    'success' => $response['success'],
                    'data' => $response['data'],
                    'debug' => [
                        'api_response' => $response,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], $response['http_code']);
            }
        }
        break;

    case 'change-password':
        if ($method === 'POST') {
            $user = TokenManager::getCurrentUser();
            
            if (!$user) {
                error_log('Password change without valid token');
                Response::json([
                    'error' => 'Unauthorized',
                    'message' => 'Valid Bearer token required',
                    'debug' => [
                        'token_valid' => false,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], 401);
            }
            
            $input = Request::input();
            error_log('Password change request from user: ' . $user['email']);
            
            $response = $restClient->post('auth/change-password', $input);
            error_log('Password change response: ' . print_r($response, true));
            
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'user' => $user['email'],
                    'api_response' => $response,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'forgot-password':
        if ($method === 'POST') {
            $input = Request::input();
            error_log('Forgot password request with data: ' . print_r($input, true));
            
            $response = $restClient->post('auth/forgot-password', $input);
            error_log('Forgot password response: ' . print_r($response, true));
            
            Response::json([
                'success' => $response['success'],
                'data' => $response['data'],
                'debug' => [
                    'api_response' => $response,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], $response['http_code']);
        }
        break;

    case 'verify-token':
        if ($method === 'GET') {
            $user = TokenManager::getCurrentUser();
            
            if ($user) {
                error_log('Token verification successful for user: ' . $user['email']);
                Response::json([
                    'valid' => true,
                    'user' => $user,
                    'expires_in' => $user['exp'] - time(),
                    'debug' => [
                        'token_valid' => true,
                        'expires_at' => date('Y-m-d H:i:s', $user['exp']),
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], 200);
            } else {
                error_log('Token verification failed');
                Response::json([
                    'valid' => false,
                    'error' => 'Invalid or expired token',
                    'debug' => [
                        'token_valid' => false,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ], 401);
            }
        }
        break;

    default:
        error_log('Unknown auth endpoint: ' . $path);
        Response::json([
            'error' => 'Auth endpoint not found',
            'debug' => [
                'requested_path' => $path,
                'available_paths' => ['login', 'register', 'logout', 'profile', 'change-password', 'forgot-password', 'verify-token'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ], 404);
        break;
}
?>