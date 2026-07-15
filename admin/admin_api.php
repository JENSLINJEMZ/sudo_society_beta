<?php

require_once '../lib/includes/Database.class.php';


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS');
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


function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}


$action = $_GET['action'] ?? '';


if ($action === 'adminLogin') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    
    
    $correct_username = 'admin';
    $correct_password = 'adminpass'; 

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Admin logged in successfully.']);
    } else {
        http_response_code(401); 
        echo json_encode(['success' => false, 'error' => 'Invalid admin credentials.']);
    }
    exit(); 
}


if (!isAdminLoggedIn()) {
    http_response_code(401); 
    echo json_encode(['success' => false, 'error' => 'Admin authentication required.']);
    exit();
}


function updateAllUserRanks($conn) {
    
    
    $sql_set_rank = "SET @rank = 0;";
    $sql_update_ranks = "UPDATE users
        SET current_rank = (@rank := @rank + 1)
        ORDER BY total_score DESC, challenges_solved DESC;";
    
    
    if (!$conn->query($sql_set_rank)) {
        error_log("Failed to initialize rank variable: " . $conn->error);
        return false;
    }
    if (!$conn->query($sql_update_ranks)) {
        error_log("Failed to update user ranks: " . $conn->error);
        return false;
    }
    return true;
}



switch ($action) {
    case 'getDashboardStats':
        try {
            $stats = [];

            
            $result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
            $stats['total_users'] = (int)$result->fetch_assoc()['total_users'];

            
            $result = $conn->query("SELECT COUNT(*) AS total_challenges FROM challenges");
            $stats['total_challenges'] = (int)$result->fetch_assoc()['total_challenges'];

            
            $result = $conn->query("SELECT COUNT(*) AS total_solves FROM solved_challenges");
            $stats['total_solves'] = (int)$result->fetch_assoc()['total_solves'];

            
            $result = $conn->query("SELECT SUM(total_score) AS total_points FROM users");
            $stats['total_points'] = (int)$result->fetch_assoc()['total_points'];

            
            $result = $conn->query("SELECT AVG(total_score) AS average_score FROM users WHERE challenges_solved > 0"); 
            $avg_score_row = $result->fetch_assoc();
            $stats['average_score'] = $avg_score_row['average_score'] ? (float)$avg_score_row['average_score'] : 0.0;

            
            $result = $conn->query("SELECT COUNT(*) AS active_challenges_count FROM challenges WHERE active = 1");
            $stats['active_challenges_count'] = (int)$result->fetch_assoc()['active_challenges_count'];


            
            $category_dist = [];
            
            $stmt = $conn->prepare("SELECT c.category, COUNT(sc.challenge_id) as solved_count 
                                    FROM solved_challenges sc
                                    JOIN challenges c ON sc.challenge_id = c.id
                                    WHERE c.active = 1
                                    GROUP BY c.category");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $category_dist[$row['category']] = (int)$row['solved_count'];
            }
            $stats['challenges_by_category'] = $category_dist;

            echo json_encode(['success' => true, 'data' => $stats]);

        } catch (Exception $e) {
            error_log("Error fetching dashboard stats: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch dashboard stats.']);
        }
        break;

    case 'getUsers':
        try {
            $users = [];
            
            $sql = "SELECT u.id, u.username, u.email, u.total_score, u.challenges_solved, u.current_rank, u.daily_streak, u.last_solved_date, u.created_at, ud.avatar_url
                    FROM users u
                    LEFT JOIN users_datas ud ON u.id = ud.user_id
                    ORDER BY u.total_score DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                
                $row['avatar_url'] = $row['avatar_url'] ?? 'https://i.imgur.com/JqYeSzn.png';
                $users[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $users]);
        } catch (Exception $e) {
            error_log("Error fetching users: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch users.']);
        }
        break;

    case 'getUserById': 
        $user_id = $_GET['id'] ?? null;
        if ($user_id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'User ID is required.']);
            exit();
        }
        try {
            $sql = "SELECT u.id, u.username, u.email, u.total_score, u.challenges_solved, u.current_rank, u.daily_streak, u.last_solved_date, u.created_at, ud.avatar_url
                    FROM users u
                    LEFT JOIN users_datas ud ON u.id = ud.user_id
                    WHERE u.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                $user['avatar_url'] = $user['avatar_url'] ?? 'https://i.imgur.com/JqYeSzn.png';
                echo json_encode(['success' => true, 'data' => $user]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'User not found.']);
            }
        } catch (Exception $e) {
            error_log("Error fetching user by ID: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch user.']);
        }
        break;

    case 'editUser':
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for editUser: " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for user edit.']);
            exit();
        }

        $user_id = $input['id'] ?? null;
        $username = $input['username'] ?? null;
        $email = $input['email'] ?? null;
        $total_score = $input['total_score'] ?? null;
        $challenges_solved = $input['challenges_solved'] ?? null;
        $daily_streak = $input['daily_streak'] ?? null;
        $last_solved_date = $input['last_solved_date'] ?? null; 

        if ($user_id === null) { 
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'User ID is required for edit.']);
            exit();
        }

        try {
            
            $conn->begin_transaction();

            $sql = "UPDATE users SET username = ?, email = ?, total_score = ?, challenges_solved = ?, daily_streak = ?, last_solved_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("ssiiisi", $username, $email, $total_score, $challenges_solved, $daily_streak, $last_solved_date, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    
                    if (!updateAllUserRanks($conn)) {
                        throw new Exception('Failed to recalculate user ranks.');
                    }
                    $conn->commit();
                    echo json_encode(['success' => true, 'message' => 'User updated successfully. Ranks recalculated.']);
                } else {
                    $conn->rollback();
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'User not found or no changes made.']);
                }
            } else {
                $conn->rollback();
                throw new Exception('Failed to update user: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error editing user: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update user. ' . $e->getMessage()]);
        }
        break;

    case 'deleteUser':
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for deleteUser: " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for user delete.']);
            exit();
        }

        $user_id = $input['id'] ?? null;

        if ($user_id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'User ID is required for delete.']);
            exit();
        }

        try {
            $conn->begin_transaction();
            
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    
                    if (!updateAllUserRanks($conn)) {
                        throw new Exception('Failed to recalculate user ranks after deletion.');
                    }
                    $conn->commit();
                    echo json_encode(['success' => true, 'message' => 'User and associated data deleted successfully. Ranks recalculated.']);
                } else {
                    $conn->rollback();
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'User not found.']);
                }
            } else {
                $conn->rollback();
                throw new Exception('Failed to delete user: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error deleting user: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete user. ' . $e->getMessage()]);
        }
        break;

    case 'getChallenges': 
        try {
            $challenges = [];
            
            $sql = "SELECT id, name, category, points, description, flag, solves, active, link FROM challenges ORDER BY id DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $challenges[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $challenges]);
        } catch (Exception $e) {
            error_log("Error fetching challenges (admin) from 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch challenges.']);
        }
        break;

    case 'getChallengeById': 
        $challenge_id = $_GET['id'] ?? null;
        if ($challenge_id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Challenge ID is required.']);
            exit();
        }
        try {
            $sql = "SELECT id, name, category, points, description, flag, solves, active, link FROM challenges WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $challenge_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $challenge = $result->fetch_assoc();
            $stmt->close();

            if ($challenge) {
                echo json_encode(['success' => true, 'data' => $challenge]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Challenge not found.']);
            }
        } catch (Exception $e) {
            error_log("Error fetching challenge by ID from 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch challenge.']);
        }
        break;

    case 'getChallengesByCategoryDetails': 
        $category = $_GET['category'] ?? null;
        if ($category === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Category parameter is required.']);
            exit();
        }
        try {
            $challenges = [];
            $sql = "SELECT id, name, points, solves FROM challenges WHERE category = ? AND active = 1 ORDER BY points DESC, name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $challenges[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $challenges]);
        } catch (Exception $e) {
            error_log("Error fetching challenges by category details from 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch challenges for category.']);
        }
        break;

    case 'addChallenge': 
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for addChallenge (to 'challenges' table): " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for add challenge.']);
            exit();
        }

        $name = $input['name'] ?? null;
        $category = $input['category'] ?? null;
        $points = $input['points'] ?? null;
        $description = $input['description'] ?? null;
        $flag = $input['flag'] ?? null;
        $link = $input['link'] ?? null;
        $active = $input['active'] ?? 1;
        $solves = $input['solves'] ?? 0; 

        if ($name === null || $category === null || $points === null || $flag === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required challenge parameters.']);
            exit();
        }

        try {
            
            $sql = "INSERT INTO challenges (name, category, points, description, flag, link, active, solves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisssii", $name, $category, $points, $description, $flag, $link, $active, $solves);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Challenge added successfully to "challenges" table.', 'id' => $stmt->insert_id]);
            } else {
                throw new Exception('Failed to add challenge to "challenges" table: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error adding challenge to 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add challenge. ' . $e->getMessage()]);
        }
        break;

    case 'editChallenge': 
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for editChallenge (for 'challenges' table): " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for edit challenge.']);
            exit();
        }

        $id = $input['id'] ?? null;
        $name = $input['name'] ?? null;
        $category = $input['category'] ?? null;
        $points = $input['points'] ?? null;
        $description = $input['description'] ?? null;
        $flag = $input['flag'] ?? null;
        $link = $input['link'] ?? null;
        $active = $input['active'] ?? null;
        
        $solves = $input['solves'] ?? null; 

        if ($id === null || $name === null || $category === null || $points === null || $flag === null || $active === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required challenge parameters for edit.']);
            exit();
        }

        try {
            
            $sql = "UPDATE challenges SET name = ?, category = ?, points = ?, description = ?, flag = ?, link = ?, active = ?, solves = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("ssisssiii", $name, $category, $points, $description, $flag, $link, $active, $solves, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Challenge updated successfully in "challenges" table.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Challenge not found or no changes made.']);
                }
            } else {
                throw new Exception('Failed to update challenge in "challenges" table: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error editing challenge in 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update challenge. ' . $e->getMessage()]);
        }
        break;

    case 'deleteChallenge': 
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for deleteChallenge (from 'challenges' table): " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for delete challenge.']);
            exit();
        }

        $id = $input['id'] ?? null;

        if ($id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Challenge ID is required for delete.']);
            exit();
        }

        try {
            
            
            $sql = "DELETE FROM challenges WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Challenge deleted successfully from "challenges" table.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Challenge not found.']);
                }
            } else {
                throw new Exception('Failed to delete challenge from "challenges" table: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error deleting challenge from 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete challenge. ' . $e->getMessage()]);
        }
        break;

    case 'toggleChallengeActive': 
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for toggleChallengeActive (for 'challenges' table): " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for toggle active.']);
            exit();
        }

        $id = $input['id'] ?? null;
        $active = $input['active'] ?? null; 

        if ($id === null || $active === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Challenge ID and active status are required.']);
            exit();
        }
        $active = (int)$active; 

        try {
            
            $sql = "UPDATE challenges SET active = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $active, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Challenge active status updated in "challenges" table.']);
                } else {
                    
                    http_response_code(404); 
                    echo json_encode(['success' => false, 'error' => 'Challenge not found or no change.']);
                }
            } else {
                throw new Exception('Failed to toggle challenge active status in "challenges" table: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error toggling challenge active status in 'challenges' table: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to toggle challenge active status. ' . $e->getMessage()]);
        }
        break;

    case 'getAchievements':
        try {
            $achievements = [];
            $sql = "SELECT id, name, description, icon, total_required FROM achievements ORDER BY id ASC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $achievements[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $achievements]);
        } catch (Exception $e) {
            error_log("Error fetching achievements (admin): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch achievements.']);
        }
        break;

    case 'getAchievementById': 
        $achievement_id = $_GET['id'] ?? null;
        if ($achievement_id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Achievement ID is required.']);
            exit();
        }
        try {
            $sql = "SELECT id, name, description, icon, total_required FROM achievements WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $achievement_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $achievement = $result->fetch_assoc();
            $stmt->close();

            if ($achievement) {
                echo json_encode(['success' => true, 'data' => $achievement]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Achievement not found.']);
            }
        } catch (Exception $e) {
            error_log("Error fetching achievement by ID: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch achievement.']);
        }
        break;

    case 'addAchievement':
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for addAchievement: " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for add achievement.']);
            exit();
        }

        $name = $input['name'] ?? null;
        $description = $input['description'] ?? null;
        $icon = $input['icon'] ?? null;
        $total_required = $input['total_required'] ?? null;

        if ($name === null || $description === null || $icon === null || $total_required === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required achievement parameters.']);
            exit();
        }

        try {
            $sql = "INSERT INTO achievements (name, description, icon, total_required) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $description, $icon, $total_required);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Achievement added successfully.', 'id' => $stmt->insert_id]);
            } else {
                throw new Exception('Failed to add achievement: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error adding achievement: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add achievement. ' . $e->getMessage()]);
        }
        break;

    case 'editAchievement':
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for editAchievement: " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for edit achievement.']);
            exit();
        }

        $id = $input['id'] ?? null;
        $name = $input['name'] ?? null;
        $description = $input['description'] ?? null;
        $icon = $input['icon'] ?? null;
        $total_required = $input['total_required'] ?? null;

        if ($id === null || $name === null || $description === null || $icon === null || $total_required === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required achievement parameters for edit.']);
            exit();
        }

        try {
            $sql = "UPDATE achievements SET name = ?, description = ?, icon = ?, total_required = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $name, $description, $icon, $total_required, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Achievement updated successfully.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Achievement not found or no changes made.']);
                }
            } else {
                throw new Exception('Failed to update achievement: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error editing achievement: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update achievement. ' . $e->getMessage()]);
        }
        break;

    case 'deleteAchievement':
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Received input for deleteAchievement: " . print_r($input, true)); 

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data received for delete achievement.']);
            exit();
        }

        $id = $input['id'] ?? null;

        if ($id === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Achievement ID is required for delete.']);
            exit();
        }

        try {
            
            $sql = "DELETE FROM achievements WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Achievement deleted successfully.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Achievement not found.']);
                }
            } else {
                throw new Exception('Failed to delete achievement: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error deleting achievement: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete achievement. ' . $e->getMessage()]);
        }
        break;

    case 'getActivityLog':
        try {
            $activity = [];
            $sql = "SELECT al.id, u.username, al.activity_type, al.description, al.points_change, al.timestamp 
                    FROM activity_log al
                    JOIN users u ON al.user_id = u.id
                    ORDER BY al.timestamp DESC
                    LIMIT 200"; 
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $activity[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $activity]);
        } catch (Exception $e) {
            error_log("Error fetching activity log: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch activity log.']);
        }
        break;

    case 'adminLogout':
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Admin logged out successfully.']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid admin action.']);
        break;
}

$conn->close();
?>
