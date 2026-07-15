<?php
require_once '../lib/includes/Database.class.php';
// Set headers for CORS and JSON content type
header('Content-Type: application/json');
// IMPORTANT: For production, specify your frontend domain instead of '*'
// Example: header('Access-Control-Allow-Origin: https://yourctf.com');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true'); // Important for sessions/cookies

// Handle preflight requests for CORS (important for some browsers/methods)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = DataBase::connection();

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Start session for managing user login state
session_start();

// Get current user ID and username from session, defaults to null if not set
$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['username'] ?? null;

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required input fields
        if (!isset($input['identifier'], $input['password'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Missing login credentials.']);
            exit();
        }

        $identifier = $input['identifier']; // Can be username or email
        $password = $input['password'];

        // Prepare statement to fetch user by username or email
        // Using prepared statements prevents SQL injection
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();


        // Verify user existence and password
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['success' => true, 'message' => 'Login successful!', 'user_id' => $user['id'], 'username' => $user['username']]);
            
        } else {
            // Login failed: Invalid credentials
            echo json_encode(['success' => false, 'message' => 'Invalid username/email or password.']);
        }
        break;

    case 'logout':
        // Destroy all session data
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        break;

    case 'checkLoginStatus':
        // Check if a user is logged in
        if ($current_user_id !== null) {
            // If user ID is in session, fetch user details for confirmation
            $stmt = $conn->prepare("SELECT id, username, avatar_url , total_score, challenges_solved FROM users WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                // User found and session is valid
                echo json_encode(['success' => true, 'loggedIn' => true, 'user' => $user]);
            } else {
                // User not found in DB (e.g., deleted), clear session
                session_unset();
                session_destroy();
                echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'User not found. Session cleared.']);
            }
        } else {
            // No user ID in session, not logged in
            echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'Not logged in.']);
        }
        break;

    default:
        // Handle invalid or unsupported actions
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}

// Close the database connection
$conn->close();
?>