<?php
require_once '../lib/includes/Database.class.php';

header('Content-Type: application/json');


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true'); 


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = DataBase::connection();


if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}


session_start();


$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['username'] ?? null;

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $input = json_decode(file_get_contents('php://input'), true);

        
        if (!isset($input['identifier'], $input['password'])) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => 'Missing login credentials.']);
            exit();
        }

        $identifier = $input['identifier']; 
        $password = $input['password'];

        
        
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();


        
        if ($user && password_verify($password, $user['password_hash'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['success' => true, 'message' => 'Login successful!', 'user_id' => $user['id'], 'username' => $user['username']]);
            
        } else {
            
            echo json_encode(['success' => false, 'message' => 'Invalid username/email or password.']);
        }
        break;

    case 'logout':
        
        session_unset(); 
        session_destroy(); 
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        break;

    case 'checkLoginStatus':
        
        if ($current_user_id !== null) {
            
            $stmt = $conn->prepare("SELECT id, username, avatar_url , total_score, challenges_solved FROM users WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                
                echo json_encode(['success' => true, 'loggedIn' => true, 'user' => $user]);
            } else {
                
                session_unset();
                session_destroy();
                echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'User not found. Session cleared.']);
            }
        } else {
            
            echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'Not logged in.']);
        }
        break;

    default:
        
        http_response_code(400); 
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}


$conn->close();
?>