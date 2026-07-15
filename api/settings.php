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



ini_set('display_errors', 0); 
ini_set('display_startup_errors', 0); 
error_reporting(E_ALL); 



$conn = DataBase::connection();


if ($conn->connect_error) {
    http_response_code(500); 
    
    error_log('Database connection failed: ' . $conn->connect_error);
    echo json_encode(['success' => false, 'error' => 'Database connection failed. Please try again later.']);
    exit();
}


session_start();


$current_user_id = $_SESSION['user_id'] ?? null;
$_SESSION['avatar_url'] = $user_settings['avatar_url'];


function requireAuth($current_user_id) {
    if ($current_user_id === null) {
        http_response_code(401); 
        echo json_encode(['success' => false, 'error' => 'Authentication required for this action.']);
        exit();
    }
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getUserSettings':
        requireAuth($current_user_id);

        
        $stmt_user = $conn->prepare("SELECT 
                                        username, email, password_hash, is_active,
                                        total_score, challenges_solved, current_rank,
                                        time_spent_hours, daily_streak, last_solved_date 
                                    FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $current_user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user_core = $result_user->fetch_assoc();
        $stmt_user->close();

        if (!$user_core) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit();
        }

        
        
        $stmt_data = $conn->prepare("SELECT
                                            bio, country, avatar_url, avatar_url,
                                            two_factor_enabled, backup_codes_generated
                                        FROM users_datas WHERE user_id = ?");
        $stmt_data->bind_param("i", $current_user_id);
        $stmt_data->execute();
        $result_data = $stmt_data->get_result();
        $user_settings = $result_data->fetch_assoc();
        $stmt_data->close();

        
        
        if (!$user_settings) {
            $conn->begin_transaction(); 
            try {
                $insert_default_stmt = $conn->prepare("INSERT INTO users_datas (user_id) VALUES (?)");
                $insert_default_stmt->bind_param("i", $current_user_id);
                $insert_default_stmt->execute();
                $insert_default_stmt->close();
                $conn->commit();

                
                
                $user_settings = [
                    'bio' => null, 'country' => null, 'avatar_url' => 'https://i.imgur.com/JqYeSzn.png',
                    'two_factor_enabled' => 0, 'backup_codes_generated' => 0
                ];
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Failed to insert default users_datas for user " . $current_user_id . ": " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to initialize user settings.']);
                exit();
            }
        }

        
        $response_data = array_merge($user_core, $user_settings);

        
        $response_data['two_factor_enabled'] = (bool)$response_data['two_factor_enabled'];
        $response_data['backup_codes_generated'] = (bool)$response_data['backup_codes_generated'];
        $response_data['is_active'] = (bool)$response_data['is_active'];
        
        
        $response_data['total_score'] = (int)$response_data['total_score'];
        $response_data['challenges_solved'] = (int)$response_data['challenges_solved'];
        $response_data['current_rank'] = (int)$response_data['current_rank'];
        $response_data['time_spent_hours'] = (int)$response_data['time_spent_hours'];
        $response_data['daily_streak'] = (int)$response_data['daily_streak'];
        
        
        unset($response_data['password_hash']);

        echo json_encode(['success' => true, 'data' => $response_data]);
        break;

    case 'updateUserSettings':
        requireAuth($current_user_id);
        
        $input = json_decode(file_get_contents('php://input'), true);

        
        $profile = $input['profile'] ?? [];
        $security = $input['security'] ?? [];
        

        
        $username = trim($profile['username'] ?? '');
        $email = trim($profile['email'] ?? '');

        
        $bio = trim($profile['bio'] ?? '');
        $country = trim($profile['country'] ?? '');
        $avatar_url = trim($profile['avatar'] ?? 'https://i.imgur.com/JqYeSzn.png'); 

        $two_factor_enabled = (int)($security['twoFactorEnabled'] ?? 0);
        $backup_codes_generated = (int)($security['backupCodesGenerated'] ?? 0); 

        
        $conn->begin_transaction();
        try {
            
            $stmt_user = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt_user->bind_param("ssi", $username, $email, $current_user_id);
            if (!$stmt_user->execute()) {
                throw new Exception('Failed to update core user data: ' . $stmt_user->error);
            }
            $stmt_user->close();

            
            
            $stmt_data = $conn->prepare("UPDATE users_datas SET
                                            bio = ?, country = ?, avatar_url = ?,
                                            two_factor_enabled = ?, backup_codes_generated = ?
                                        WHERE user_id = ?");
            $stmt_data->bind_param("sssiii",
                $bio, $country, $avatar_url,
                $two_factor_enabled, $backup_codes_generated,
                $current_user_id
            );

            if (!$stmt_data->execute()) {
                throw new Exception('Failed to update user settings data: ' . $stmt_data->error);
            }
            $stmt_data->close();
            $conn->commit(); 
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);

        } catch (Exception $e) {
            $conn->rollback(); 
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save settings: ' . $e->getMessage()]);
            error_log("Failed to save settings for user " . $current_user_id . ": " . $e->getMessage());
        }
        break;

    case 'changePassword':
        requireAuth($current_user_id);
        $input = json_decode(file_get_contents('php://input'), true);

        $current_password = $input['currentPassword'] ?? '';
        $new_password = $input['newPassword'] ?? '';
        $confirm_new_password = $input['confirmNewPassword'] ?? '';

        
        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit();
        }
        if ($new_password !== $confirm_new_password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
            exit();
        }
        if (strlen($new_password) < 8) { 
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
            exit();
        }

        
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
            exit();
        }

        
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed_password, $current_user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to change password.']);
            error_log("Failed to change password for user " . $current_user_id . ": " . $stmt->error);
        }
        $stmt->close();
        break;

    case 'uploadAvatar':
        requireAuth($current_user_id);

        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
            exit();
        }

        $file = $_FILES['avatar'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; 

        if (!in_array($file['type'], $allowed_types)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed.']);
            exit();
        }
        if ($file['size'] > $max_size) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB limit.']);
            exit();
        }

        $upload_dir = 'uploads/avatars/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); 
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        $new_filename = uniqid('avatar_') . bin2hex(random_bytes(4)) . '.' . $file_extension;
        $destination = $upload_dir . $new_filename;
        
        $avatar_url = 'http://192.168.1.2/Sudo_society/' . $destination; 

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            
            $stmt_get_old_avatar = $conn->prepare("SELECT avatar_url FROM users_datas WHERE user_id = ?");
            $stmt_get_old_avatar->bind_param("i", $current_user_id);
            $stmt_get_old_avatar->execute();
            $result_old_avatar = $stmt_get_old_avatar->get_result();
            $old_avatar_data = $result_old_avatar->fetch_assoc();
            $stmt_get_old_avatar->close();

            if ($old_avatar_data && $old_avatar_data['avatar_url'] && $old_avatar_data['avatar_url'] !== 'https://i.imgur.com/JqYeSzn.png') {
                
                $old_path_to_delete = str_replace('http://192.168.1.2/Sudo_society/api/', '', $old_avatar_data['avatar_url']); 
                if (file_exists($old_path_to_delete) && !is_dir($old_path_to_delete)) {
                    unlink($old_path_to_delete);
                }
            }

            
            $stmt = $conn->prepare("UPDATE users_datas SET avatar_url = ? WHERE user_id = ?");
            $stmt->bind_param("si", $avatar_url, $current_user_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Avatar uploaded successfully!', 'avatar_url' => $avatar_url]);
            } else {
                
                unlink($destination);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update avatar URL in database.']);
                error_log("Failed to update avatar URL for user " . $current_user_id . ": " . $stmt->error);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
        }
        break;

    case 'removeAvatar':
        requireAuth($current_user_id);

        $default_avatar_url = 'https://i.imgur.com/JqYeSzn.png'; 

        
        $stmt = $conn->prepare("SELECT avatar_url FROM users_datas WHERE user_id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();

        if ($user_data && $user_data['avatar_url'] && $user_data['avatar_url'] !== $default_avatar_url) {
            
            $path_to_delete = str_replace('http://192.168.1.2/Sudo_society/api/', '', $user_data['avatar_url']); 
            if (file_exists($path_to_delete) && !is_dir($path_to_delete)) {
                unlink($path_to_delete);
            }
        }

        
        $stmt = $conn->prepare("UPDATE users_datas SET avatar_url = ? WHERE user_id = ?");
        $stmt->bind_param("si", $default_avatar_url, $current_user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Avatar removed successfully!', 'avatar_url' => $default_avatar_url]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to remove avatar.']);
            error_log("Failed to remove avatar for user " . $current_user_id . ": " . $stmt->error);
        }
        $stmt->close();
        break;

    
    

    case 'deactivateAccount':
        requireAuth($current_user_id);
        

        
        $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->bind_param("i", $current_user_id);
        if ($stmt->execute()) {
            
            session_unset();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Account deactivated successfully. You have been logged out.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to deactivate account.']);
            error_log("Failed to deactivate account for user " . $current_user_id . ": " . $stmt->error);
        }
        $stmt->close();
        break;

    case 'deleteAccount':
        requireAuth($current_user_id);
        
        
        $input = json_decode(file_get_contents('php://input'), true);
        $confirmation_text = $input['confirmationText'] ?? '';

        
        if ($confirmation_text !== 'DELETE MY ACCOUNT' && $confirmation_text !== 'DELETE MY DATA') { 
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Incorrect confirmation text. Account deletion cancelled.']);
            exit();
        }

        
        
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $current_user_id);
        if ($stmt->execute()) {
            
            session_unset();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Your account has been permanently deleted.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete account.']);
            error_log("Failed to delete account for user " . $current_user_id . ": " . $stmt->error);
        }
        $stmt->close();
        break;
    
    
    
    

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}


$conn->close();
?>
