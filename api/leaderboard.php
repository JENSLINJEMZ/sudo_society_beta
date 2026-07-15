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

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            http_response_code(409); 
            echo json_encode(['success' => false, 'message' => 'Username or Email already exists.']);
            $stmt->close();
            exit();
        }
        $stmt->close();

        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, is_active) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id; 
            echo json_encode(['success' => true, 'message' => 'Registration successful!', 'user_id' => $_SESSION['user_id']]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'login':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            exit();
        }

        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['success' => true, 'message' => 'Login successful!', 'user_id' => $user['id']]);
        } else {
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
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
                    c.day_label,
                    c.story,
                    c.learning_objectives_json,
                    c.learning_details_html,
                    c.resources_json,
                    c.questions_json,
                    c.machine_link,
                    FALSE AS solved
                FROM
                    event_challenges c
                WHERE
                    c.active = 1
                ORDER BY
                    c.id ASC
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
                    c.day_label,
                    c.story,
                    c.learning_objectives_json,
                    c.learning_details_html,
                    c.resources_json,
                    c.questions_json,
                    c.machine_link,
                    CASE WHEN sc.user_id IS NOT NULL THEN TRUE ELSE FALSE END AS solved
                FROM
                    event_challenges c
                LEFT JOIN
                    solved_challenges sc ON c.id = sc.challenge_id AND sc.user_id = ?
                WHERE
                    c.active = 1
                ORDER BY
                    c.id ASC
            ";
            $stmt = $conn->prepare($sql);
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

                
                $row['learning_objectives'] = json_decode($row['learning_objectives_json'], true) ?? [];
                $row['resources'] = json_decode($row['resources_json'], true) ?? [];
                $row['questions'] = json_decode($row['questions_json'], true) ?? [];

                
                unset($row['learning_objectives_json']);
                unset($row['resources_json']);
                unset($row['questions_json']);

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
            echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a flag.']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['challenge_id'], $input['flag'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing flag submission parameters.']);
            exit();
        }

        $challenge_id = (int)$input['challenge_id'];
        $submitted_flag = $input['flag'];

        $conn->begin_transaction();
        $success = false;
        $message = '';

        try {
            
            $stmt = $conn->prepare("SELECT id, name, category, points, flag FROM event_challenges WHERE id = ?");
            $stmt->bind_param("i", $challenge_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $challenge = $result->fetch_assoc();
            $stmt->close();

            if (!$challenge) {
                $message = 'Challenge not found.';
                throw new Exception($message);
            }

            
            $stmt = $conn->prepare("SELECT id FROM solved_challenges WHERE user_id = ? AND challenge_id = ?");
            $stmt->bind_param("ii", $current_user_id, $challenge_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $message = 'Challenge already solved by you.';
                throw new Exception($message);
            }
            $stmt->close();

            
            if ($submitted_flag !== $challenge['flag']) {
                $message = 'Incorrect Flag.';
                throw new Exception($message);
            }

            
            $challenge_points = (int)$challenge['points'];

            
            $stmt = $conn->prepare("INSERT INTO solved_challenges (user_id, challenge_id, timestamp) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $current_user_id, $challenge_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to record solved challenge: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("UPDATE event_challenges SET solves = solves + 1 WHERE id = ?");
            $stmt->bind_param("i", $challenge_id);
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
            $stmt->bind_param("ii", $challenge_points, $current_user_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update user score: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("SELECT total_score FROM users WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $user_current_score_data = $stmt->get_result()->fetch_assoc();
            $user_current_score = $user_current_score_data['total_score'];
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO score_history (user_id, score, timestamp) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $current_user_id, $user_current_score);
            if (!$stmt->execute()) {
                throw new Exception('Failed to add to score history: ' . $stmt->error);
            }
            $stmt->close();

            
            $activity_desc = "Solved \"" . $challenge['name'] . "\" (" . $challenge['category'] . ")";
            $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, points_change, timestamp) VALUES (?, 'solved', ?, ?, NOW())");
            $stmt->bind_param("isi", $current_user_id, $activity_desc, $challenge_points);
            if (!$stmt->execute()) {
                throw new Exception('Failed to add to activity log: ' . $stmt->error);
            }
            $stmt->close();

            
            $stmt = $conn->prepare("SELECT challenges_solved FROM users WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $user_data = $stmt->get_result()->fetch_assoc();
            $user_solved_count = $user_data['challenges_solved'];
            $stmt->close();

            
            if ($user_solved_count == 1) {
                $stmt = $conn->prepare("SELECT id FROM achievements WHERE name = 'First Blood'");
                $stmt->execute();
                $achievement = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($achievement) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress, unlocked_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE progress = VALUES(progress), unlocked_at = VALUES(unlocked_at)");
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
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS crypto_solves FROM solved_challenges sc JOIN event_challenges c ON sc.challenge_id = c.id WHERE sc.user_id = ? AND c.category = 'crypto'");
                $stmt->bind_param("i", $current_user_id);
                $stmt->execute();
                $crypto_solves_data = $stmt->get_result()->fetch_assoc();
                $crypto_solves_count = $crypto_solves_data['crypto_solves'];
                $stmt->close();

                $stmt = $conn->prepare("SELECT id, total_required FROM achievements WHERE name = 'Crypto Master'");
                $stmt->execute();
                $crypto_master_ach = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($crypto_master_ach) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress = VALUES(progress)");
                    $stmt->bind_param("iii", $current_user_id, $crypto_master_ach['id'], $crypto_solves_count);
                    if (!$stmt->execute()) {
                        error_log("Failed to update Crypto Master achievement progress: " . $stmt->error);
                    }
                    $stmt->close();

                    if ($crypto_solves_count >= $crypto_master_ach['total_required']) {
                        $stmt = $conn->prepare("UPDATE user_achievements SET unlocked_at = NOW() WHERE user_id = ? AND achievement_id = ? AND unlocked_at IS NULL");
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
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS web_solves FROM solved_challenges sc JOIN event_challenges c ON sc.challenge_id = c.id WHERE sc.user_id = ? AND c.category = 'web'");
                $stmt->bind_param("i", $current_user_id);
                $stmt->execute();
                $web_solves_data = $stmt->get_result()->fetch_assoc();
                $web_solves_count = $web_solves_data['web_solves'];
                $stmt->close();

                $stmt = $conn->prepare("SELECT id, total_required FROM achievements WHERE name = 'Web Wizard'");
                $stmt->execute();
                $web_wizard_ach = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($web_wizard_ach) {
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_id, progress) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress = VALUES(progress)");
                    $stmt->bind_param("iii", $current_user_id, $web_wizard_ach['id'], $web_solves_count);
                    if (!$stmt->execute()) {
                        error_log("Failed to update Web Wizard achievement progress: " . $stmt->error);
                    }
                    $stmt->close();

                    if ($web_solves_count >= $web_wizard_ach['total_required']) {
                        $stmt = $conn->prepare("UPDATE user_achievements SET unlocked_at = NOW() WHERE user_id = ? AND achievement_id = ? AND unlocked_at IS NULL");
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
            error_log("Flag submission failed for user " . $current_user_id . ": " . $message);
        }

        echo json_encode(['success' => $success, 'message' => $message]);
        break;

    case 'getLeaderboard':
        $sql = "
            SELECT
                username,
                total_score,
                challenges_solved
            FROM
                users
            ORDER BY
                total_score DESC,
                challenges_solved DESC
            LIMIT 20; -- Limit to top 20 users for the leaderboard
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $leaderboard = [];
            while ($row = $result->fetch_assoc()) {
                $leaderboard[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $leaderboard]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch leaderboard: ' . $conn->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

$conn->close();
?>
