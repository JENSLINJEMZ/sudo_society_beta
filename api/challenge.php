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
    
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500); 
    echo json_encode(['success' => false, 'error' => 'Database connection failed. Please try again later.']);
    exit();
}


session_start();





$current_user_id = $_SESSION['user_id'] ?? null;


$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'checkLoginStatus':
        if ($current_user_id !== null) {
            
            $stmt = $conn->prepare("SELECT id, username, total_score, challenges_solved FROM users WHERE id = ?");
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for checkLoginStatus: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                
                $user['id'] = (int)$user['id'];
                $user['total_score'] = (int)$user['total_score'];
                $user['challenges_solved'] = (int)$user['challenges_solved'];
                echo json_encode(['success' => true, 'loggedIn' => true, 'user' => $user]);
            } else {
                
                session_unset(); 
                session_destroy(); 
                echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'User not found or session expired.']);
            }
        } else {
            echo json_encode(['success' => true, 'loggedIn' => false, 'message' => 'Not logged in.']);
        }
        break;

    case 'getChallenges':
        
        if ($current_user_id === null) {
            
            $sql = "
                SELECT
                    c.id,
                    c.name AS title,
                    c.category,
                    c.points,
                    c.solves,
                    c.description,
                    c.link,
                    FALSE AS solved -- Always false if no user is logged in
                FROM
                    challenges c
                WHERE
                    c.active = 1
                ORDER BY
                    c.points ASC
            ";
            $stmt = $conn->prepare($sql);
        } else {
            
            
            $sql = "
                SELECT
                    c.id,
                    c.name AS title,
                    c.category,
                    c.points,
                    c.solves,
                    c.description,
                    c.link,
                    CASE WHEN sc.user_id IS NOT NULL THEN TRUE ELSE FALSE END AS solved
                FROM
                    challenges c
                LEFT JOIN
                    solved_challenges sc ON c.id = sc.challenges_id AND sc.user_id = ?
                WHERE
                    c.active = 1
                ORDER BY
                    c.points ASC
            ";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getChallenges: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param("i", $current_user_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $challenges = [];
            while ($row = $result->fetch_assoc()) {
                $row['id'] = (int)$row['id'];
                $row['points'] = (int)$row['points'];
                $row['solves'] = (int)$row['solves'];
                $row['solved'] = (bool)$row['solved'];
                $challenges[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $challenges]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch challenges: ' . $conn->error]);
        }
        $stmt->close();
        break;

    case 'submitFlag':
        if ($current_user_id === null) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'You must be logged in to submit a flag.']);
            exit();
        }

        
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON input.']);
            exit();
        }

        $challenge_id_from_frontend = filter_var($input['challenge_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $submitted_flag = trim($input['flag'] ?? '');
        

        
        error_log("DEBUG: Submitted challenge_id_from_frontend: " . $challenge_id_from_frontend);
        error_log("DEBUG: Submitted flag (trimmed): '" . $submitted_flag . "'");
        

        if (empty($challenge_id_from_frontend) || empty($submitted_flag)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing flag submission parameters.']);
            exit();
        }

        $conn->begin_transaction();
        $success = false;
        $message = '';

        try {
            
            $stmt = $conn->prepare("SELECT id, name, category, points, flag FROM challenges WHERE id = ? AND active = 1");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for challenge details: ' . $conn->error);
            }
            $stmt->bind_param("i", $challenge_id_from_frontend);
            $stmt->execute();
            $result = $stmt->get_result();
            $challenge = $result->fetch_assoc();
            $stmt->close();

            if (!$challenge) {
                $message = 'Challenge not found or not active.';
                throw new Exception($message);
            }

            $correct_flag_from_db = $challenge['flag'];

            
            error_log("DEBUG: Correct flag from DB: '" . $correct_flag_from_db . "'");
            error_log("DEBUG: Comparing submitted flag ('" . $submitted_flag . "') with correct flag ('" . $correct_flag_from_db . "')");
            

            
            $stmt = $conn->prepare("SELECT id FROM solved_challenges WHERE user_id = ? AND challenges_id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for solved check: ' . $conn->error);
            }
            $stmt->bind_param("ii", $current_user_id, $challenge_id_from_frontend);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $message = 'Challenge already solved by you.';
                throw new Exception($message);
            }
            $stmt->close();

            
            if ($submitted_flag !== $correct_flag_from_db) { 
                $message = 'Incorrect Flag.';
                throw new Exception($message);
            }

            $challenge_points = (int)$challenge['points'];

            
            
            $stmt = $conn->prepare("INSERT INTO solved_challenges (user_id, challenges_id, timestamp) VALUES (?, ?, NOW())");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for solved_challenges insert: ' . $conn->error);
            }
            $stmt->bind_param("ii", $current_user_id, $challenge_id_from_frontend);
            if (!$stmt->execute()) {
                throw new Exception('Failed to record solved challenge: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("UPDATE challenges SET solves = solves + 1 WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for challenges update: ' . $conn->error);
            }
            $stmt->bind_param("i", $challenge_id_from_frontend);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update challenge solves count: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("UPDATE users SET
                                        total_score = total_score + ?,
                                        challenges_solved = challenges_solved + 1,
                                        last_solved_date = CURDATE(),
                                        daily_streak = CASE WHEN last_solved_date = CURDATE() - INTERVAL 1 DAY THEN daily_streak + 1 ELSE 1 END
                                        WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for user update: ' . $conn->error);
            }
            $stmt->bind_param("ii", $challenge_points, $current_user_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update user score: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("SELECT total_score FROM users WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for user score fetch: ' . $conn->error);
            }
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $user_current_score_data = $stmt->get_result()->fetch_assoc();
            $user_current_score = (int)$user_current_score_data['total_score'];
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO score_history (user_id, score, timestamp) VALUES (?, ?, NOW())");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for score history insert: ' . $conn->error);
            }
            $stmt->bind_param("ii", $current_user_id, $user_current_score);
            if (!$stmt->execute()) {
                throw new Exception('Failed to add to score history: ' . $stmt->error);
            }
            $stmt->close();

            
            $activity_desc = "Solved \"" . htmlspecialchars($challenge['name']) . "\" (" . htmlspecialchars($challenge['category']) . ")";
            $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, points_change, timestamp) VALUES (?, 'solved', ?, ?, NOW())");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for activity log insert: ' . $conn->error);
            }
            $stmt->bind_param("isi", $current_user_id, $activity_desc, $challenge_points);
            if (!$stmt->execute()) {
                error_log("Failed to add to activity log: " . $stmt->error); 
            }
            $stmt->close();

            
            $stmt = $conn->prepare("SELECT challenges_solved FROM users WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare statement for user solved count: ' . $conn->error);
            }
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $user_data = $stmt->get_result()->fetch_assoc();
            $user_solved_count = (int)$user_data['challenges_solved'];
            $stmt->close();

            
            if ($user_solved_count === 1) { 
                $stmt = $conn->prepare("SELECT id FROM achievements WHERE name = 'First Blood'");
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement for First Blood achievement: ' . $conn->error);
                }
                $stmt->execute();
                $achievement = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($achievement) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress, unlocked_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE progress = VALUES(progress), unlocked_at = VALUES(unlocked_at)");
                    if (!$stmt) {
                        throw new Exception('Failed to prepare statement for user_achievements insert/update (First Blood): ' . $conn->error);
                    }
                    $stmt->bind_param("ii", $current_user_id, $achievement['id']);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $activity_desc = "Unlocked \"First Blood\" achievement";
                        $stmt_log = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, timestamp) VALUES (?, 'achievement_unlocked', ?, NOW())");
                        $stmt_log->bind_param("is", $current_user_id, $activity_desc);
                        $stmt_log->execute();
                        $stmt_log->close();
                    }
                    $stmt->close();
                }
            }

            
            if ($challenge['category'] === 'crypto') {
                
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS crypto_solves FROM solved_challenges sc JOIN challenges c ON sc.challenges_id = c.id WHERE sc.user_id = ? AND c.category = 'crypto'");
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement for crypto solves count: ' . $conn->error);
                }
                $stmt->bind_param("i", $current_user_id);
                $stmt->execute();
                $crypto_solves_data = $stmt->get_result()->fetch_assoc();
                $crypto_solves_count = (int)$crypto_solves_data['crypto_solves'];
                $stmt->close();

                $stmt = $conn->prepare("SELECT id, total_required FROM achievements WHERE name = 'Crypto Master'");
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement for Crypto Master achievement: ' . $conn->error);
                }
                $stmt->execute();
                $crypto_master_ach = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($crypto_master_ach) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress = VALUES(progress)");
                    if (!$stmt) {
                        throw new Exception('Failed to prepare statement for user_achievements insert/update (Crypto Master progress): ' . $conn->error);
                    }
                    $stmt->bind_param("iii", $current_user_id, $crypto_master_ach['id'], $crypto_solves_count);
                    if (!$stmt->execute()) {
                        error_log("Failed to update Crypto Master achievement progress: " . $stmt->error);
                    }
                    $stmt->close();

                    if ($crypto_solves_count >= $crypto_master_ach['total_required']) {
                        $stmt = $conn->prepare("UPDATE user_achievements SET unlocked_at = NOW() WHERE user_id = ? AND achievement_id = ? AND unlocked_at IS NULL");
                        if (!$stmt) {
                            throw new Exception('Failed to prepare statement for user_achievements unlock (Crypto Master): ' . $conn->error);
                        }
                        $stmt->bind_param("ii", $current_user_id, $crypto_master_ach['id']);
                        if ($stmt->execute() && $stmt->affected_rows > 0) {
                            $activity_desc = "Unlocked \"Crypto Master\" achievement";
                            $stmt_log = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, timestamp) VALUES (?, 'achievement_unlocked', ?, NOW())");
                            $stmt_log->bind_param("is", $current_user_id, $activity_desc);
                            $stmt_log->execute();
                            $stmt_log->close();
                        }
                        $stmt->close();
                    }
                }
            }

            
            if ($challenge['category'] === 'web') {
                
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS web_solves FROM solved_challenges sc JOIN challenges c ON sc.challenges_id = c.id WHERE sc.user_id = ? AND c.category = 'web'");
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement for web solves count: ' . $conn->error);
                }
                $stmt->bind_param("i", $current_user_id);
                $stmt->execute();
                $web_solves_data = $stmt->get_result()->fetch_assoc();
                $web_solves_count = (int)$web_solves_data['web_solves'];
                $stmt->close();

                $stmt = $conn->prepare("SELECT id, total_required FROM achievements WHERE name = 'Web Wizard'");
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement for Web Wizard achievement: ' . $conn->error);
                }
                $stmt->execute();
                $web_wizard_ach = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($web_wizard_ach) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress = VALUES(progress)");
                    if (!$stmt) {
                        throw new Exception('Failed to prepare statement for user_achievements insert/update (Web Wizard progress): ' . $conn->error);
                    }
                    $stmt->bind_param("iii", $current_user_id, $web_wizard_ach['id'], $web_solves_count);
                    if (!$stmt->execute()) {
                        error_log("Failed to update Web Wizard achievement progress: " . $stmt->error);
                    }
                    $stmt->close();

                    if ($web_solves_count >= $web_wizard_ach['total_required']) {
                        $stmt = $conn->prepare("UPDATE user_achievements SET unlocked_at = NOW() WHERE user_id = ? AND achievement_id = ? AND unlocked_at IS NULL");
                        if (!$stmt) {
                            throw new Exception('Failed to prepare statement for user_achievements unlock (Web Wizard): ' . $conn->error);
                        }
                        $stmt->bind_param("ii", $current_user_id, $web_wizard_ach['id']);
                        if ($stmt->execute() && $stmt->affected_rows > 0) {
                            $activity_desc = "Unlocked \"Web Wizard\" achievement";
                            $stmt_log = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, timestamp) VALUES (?, 'achievement_unlocked', ?, NOW())");
                            $stmt_log->bind_param("is", $current_user_id, $activity_desc);
                            $stmt_log->execute();
                            $stmt_log->close();
                        }
                        $stmt->close();
                    }
                }
            }

            $conn->commit();
            $success = true;
            $message = 'Correct Flag! Challenge solved.';

        } catch (Exception $e) {
            $conn->rollback(); 
            $message = $e->getMessage();
            error_log("Flag submission failed for user " . ($current_user_id ?? 'N/A') . ", Challenge ID: " . $challenge_id_from_frontend . ": " . $message);
            
            if ($message === 'Challenge not found or not active.' || $message === 'Challenge already solved by you.' || $message === 'Incorrect Flag.') {
                 
            } else {
                 $message = 'An internal server error occurred during submission. Please try again.';
            }
            http_response_code(500); 
        }

        echo json_encode(['success' => $success, 'message' => $message]);
        break;

    default:
        http_response_code(400); 
        echo json_encode(['success' => false, 'error' => 'Invalid action specified.']);
        break;
}

$conn->close();
?>