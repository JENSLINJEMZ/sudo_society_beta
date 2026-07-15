<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true'); 


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$db_host = 'localhost';
$db_port = '3306';
$db_user = 'phpmyadmin';     
$db_pass = 'kali';           
$db_name = 'phpmyadmin'; 


$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);


if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}


session_start();





$current_user_id = $_SESSION['user_id'] ?? null; 

$action = $_GET['action'] ?? '';

switch ($action) {
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
                    solved_challenges sc ON c.id = sc.challenge_id AND sc.user_id = ?
                WHERE
                    c.active = 1
                ORDER BY
                    c.points ASC
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

        if (!isset($input['challenge_id'], $input['flag'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing flag submission parameters.']);
            exit();
        }

        $challenge_id = (int)$input['challenge_id'];
        $submitted_flag = $input['flag'];

        
        $conn->begin_transaction();
        $success = false;
        $message = '';

        try {
            
            $stmt = $conn->prepare("SELECT id, name, category, points, flag FROM challenges WHERE id = ?");
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

            
            $stmt = $conn->prepare("UPDATE challenges SET solves = solves + 1 WHERE id = ?");
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
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS crypto_solves FROM solved_challenges sc JOIN challenges c ON sc.challenge_id = c.id WHERE sc.user_id = ? AND c.category = 'crypto'");
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
                $stmt = $conn->prepare("SELECT COUNT(sc.id) AS web_solves FROM solved_challenges sc JOIN challenges c ON sc.challenge_id = c.id WHERE sc.user_id = ? AND c.category = 'web'");
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

    

    case 'get_all_challenges_admin':
        
        
        
        
        
        

        $sql = "SELECT id, name, category, points, description, flag, solves, active, link, created_at, day_label, story, learning_objectives_json, learning_details_html, resources_json, questions_json, machine_link FROM event_challenges ORDER BY created_at DESC";
        $result = $conn->query($sql);

        $challenges = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                
                $row['active'] = (bool)$row['active'];
                $challenges[] = $row;
            }
            echo json_encode(['success' => true, 'challenges' => $challenges]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch challenges: ' . $conn->error]);
        }
        break;

    case 'get_challenge_by_id':
        
        
        
        
        
        

        $challenge_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$challenge_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid challenge ID.']);
            exit();
        }

        $stmt = $conn->prepare("SELECT id, name, category, points, description, flag, solves, active, link, created_at, day_label, story, learning_objectives_json, learning_details_html, resources_json, questions_json, machine_link FROM event_challenges WHERE id = ?");
        $stmt->bind_param("i", $challenge_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $challenge = $result->fetch_assoc();
        $stmt->close();

        if ($challenge) {
            $challenge['active'] = (bool)$challenge['active'];
            echo json_encode(['success' => true, 'challenge' => $challenge]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Challenge not found.']);
        }
        break;

    case 'add_challenge':
        
        
        
        
        
        

        $input = json_decode(file_get_contents('php://input'), true);

        
        if (!isset($input['name'], $input['category'], $input['points'], $input['flag'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields (name, category, points, flag).']);
            exit();
        }

        $name = $input['name'];
        $category = $input['category'];
        $points = (int)$input['points'];
        $description = $input['description'] ?? null;
        $flag = $input['flag'];
        $solves = (int)($input['solves'] ?? 0);
        $active = (int)($input['active'] ?? 1); 
        $link = $input['link'] ?? null;
        $day_label = $input['day_label'] ?? null;
        $story = $input['story'] ?? null;
        $learning_objectives_json = $input['learning_objectives_json'] ?? null;
        $learning_details_html = $input['learning_details_html'] ?? null;
        $resources_json = $input['resources_json'] ?? null;
        $questions_json = $input['questions_json'] ?? null;
        $machine_link = $input['machine_link'] ?? null;


        $stmt = $conn->prepare("INSERT INTO event_challenges (name, category, points, description, flag, solves, active, link, day_label, story, learning_objectives_json, learning_details_html, resources_json, questions_json, machine_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssisisiisssssss", $name, $category, $points, $description, $flag, $solves, $active, $link, $day_label, $story, $learning_objectives_json, $learning_details_html, $resources_json, $questions_json, $machine_link);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Challenge added successfully!', 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add challenge: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'update_challenge':
        
        
        
        
        
        

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id'], $input['name'], $input['category'], $input['points'], $input['flag'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields (id, name, category, points, flag).']);
            exit();
        }

        $id = (int)$input['id'];
        $name = $input['name'];
        $category = $input['category'];
        $points = (int)$input['points'];
        $description = $input['description'] ?? null;
        $flag = $input['flag'];
        $solves = (int)($input['solves'] ?? 0);
        $active = (int)($input['active'] ?? 1);
        $link = $input['link'] ?? null;
        $day_label = $input['day_label'] ?? null;
        $story = $input['story'] ?? null;
        $learning_objectives_json = $input['learning_objectives_json'] ?? null;
        $learning_details_html = $input['learning_details_html'] ?? null;
        $resources_json = $input['resources_json'] ?? null;
        $questions_json = $input['questions_json'] ?? null;
        $machine_link = $input['machine_link'] ?? null;


        $stmt = $conn->prepare("UPDATE event_challenges SET name=?, category=?, points=?, description=?, flag=?, solves=?, active=?, link=?, day_label=?, story=?, learning_objectives_json=?, learning_details_html=?, resources_json=?, questions_json=?, machine_link=? WHERE id=?");

        $stmt->bind_param("ssisisiisssssssi", $name, $category, $points, $description, $flag, $solves, $active, $link, $day_label, $story, $learning_objectives_json, $learning_details_html, $resources_json, $questions_json, $machine_link, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Challenge updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Challenge not found or no changes made.']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update challenge: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'delete_challenge':
        
        
        
        
        
        

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing challenge ID.']);
            exit();
        }

        $id = (int)$input['id'];

        $stmt = $conn->prepare("DELETE FROM event_challenges WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Challenge deleted successfully!']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Challenge not found.']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete challenge: ' . $stmt->error]);
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
