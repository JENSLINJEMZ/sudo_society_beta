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

// Establish database connection
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
    case 'register':
        $input = json_decode(file_get_contents('php://input'), true);

        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirmPassword'] ?? ''; // Added for server-side match check

        // --- Server-side Validation ---
        $errors = [];

        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $errors[] = 'All fields are required.';
        }
        if (strlen($username) < 4 || strlen($username) > 16) {
            $errors[] = 'Username must be between 4 and 16 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        // Check if username or email already exists
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $existingUser = $result->fetch_assoc();
                // Determine if username or email is taken
                $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_check_username->bind_param("s", $username);
                $stmt_check_username->execute();
                $result_username = $stmt_check_username->get_result();
                if ($result_username->num_rows > 0) {
                    $errors[] = 'Username already taken.';
                }
                $stmt_check_username->close();

                $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_check_email->bind_param("s", $email);
                $stmt_check_email->execute();
                $result_email = $stmt_check_email->get_result();
                if ($result_email->num_rows > 0) {
                    $errors[] = 'Email already registered.';
                }
                $stmt_check_email->close();
            }
            $stmt->close();
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Registration failed.', 'errors' => $errors]);
            exit();
        }

        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            // Registration successful - automatically log the user in
            $_SESSION['user_id'] = $conn->insert_id; // Get the ID of the newly inserted user
            $_SESSION['username'] = $username;
            echo json_encode(['success' => true, 'message' => 'Registration successful! You are now logged in.', 'user_id' => $_SESSION['user_id'], 'username' => $_SESSION['username']]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Registration failed due to a server error. Please try again later.']);
            error_log("User registration failed: " . $stmt->error); // Log the actual database error
        }
        $stmt->close();
        break;

    case 'checkLoginStatus':
        if ($current_user_id !== null) {
            $stmt = $conn->prepare("SELECT id, username, total_score, challenges_solved FROM users WHERE id = ?");
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