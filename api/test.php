<?php
require_once '../lib/includes/Database.class.php';

header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');


define('DB_HOST', 'localhost:3306');
define('DB_USER', 'phpmyadmin'); 
define('DB_PASS', 'kali'); 
define('DB_NAME', 'phpmyadmin'); 


$conn = DataBase::connection();


if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}
session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

$user_id = $_SESSION['user_id'] ?? null;




switch ($action) {

    
    case 'getUserStats':
        $sql = "SELECT username, total_score, challenges_solved, current_rank, time_spent_hours, daily_streak, last_solved_date FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            
            
            $lastSolvedDate = new DateTime($user['last_solved_date']);
            $today = new DateTime();
            $yesterday = new DateTime('yesterday');

            if ($lastSolvedDate->format('Y-m-d') < $yesterday->format('Y-m-d')) {
                
                $user['daily_streak'] = 0;
            }

            echo json_encode(['success' => true, 'data' => $user]);
        } else {
            http_response_code(404); 
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
        $stmt->close();
        break;

    
    case 'getScoreProgression':
        $period = isset($_GET['period']) ? $_GET['period'] : '7d';
        $time_interval = '7 DAY'; 

        switch ($period) {
            case '30d': $time_interval = '30 DAY'; break;
            case '90d': $time_interval = '90 DAY'; break;
            case 'all': $time_interval = '10 YEAR'; break; 
        }
        
        $sql = "SELECT score, timestamp FROM score_history WHERE user_id = ? AND timestamp >= NOW() - INTERVAL " . $time_interval . " ORDER BY timestamp ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $data]);
        $stmt->close();
        break;

    
    case 'getChallengesByCategory':
        $sql = "SELECT c.category, COUNT(sc.challenge_id) as solved_count 
                FROM solved_challenges sc
                JOIN challenges c ON sc.challenge_id = c.id
                WHERE sc.user_id = ?
                GROUP BY c.category";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[$row['category']] = (int)$row['solved_count'];
        }

        
        $series = array_values($data);
        $labels = array_keys($data);

        echo json_encode(['success' => true, 'series' => $series, 'labels' => $labels]);
        $stmt->close();
        break;
    
    
    case 'getTotalChallenges':
        $sql = "SELECT COUNT(*) as total_challenges FROM challenges";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        echo json_encode(['success' => true, 'data' => (int)$row['total_challenges']]);
        break;

    
    case 'getLatestAchievements':
        $sql = "SELECT a.name, a.description, a.icon, a.total_required, ua.progress, ua.unlocked_at
                FROM user_achievements ua
                JOIN achievements a ON ua.achievement_id = a.id
                WHERE ua.user_id = ?
                ORDER BY ua.unlocked_at DESC, ua.progress DESC
                LIMIT 4"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $achievements = [];
        while ($row = $result->fetch_assoc()) {
            $row['progress_percent'] = ($row['total_required'] > 0) ? round(($row['progress'] / $row['total_required']) * 100) : 100;
            $achievements[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $achievements]);
        $stmt->close();
        break;
    
    
    case 'getRecentActivity':
        $sql = "SELECT activity_type, description, points_change, timestamp 
                FROM activity_log
                WHERE user_id = ?
                ORDER BY timestamp DESC
                LIMIT 5"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $activity = [];
        while ($row = $result->fetch_assoc()) {
            $activity[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $activity]);
        $stmt->close();
        break;

    
    case 'getLeaderboard':
        $sql = "SELECT current_rank AS rank, username, total_score AS score FROM users ORDER BY total_score DESC LIMIT 100"; 
        $result = $conn->query($sql);
        
        $leaderboard = [];
        while ($row = $result->fetch_assoc()) {
            $leaderboard[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $leaderboard]);
        break;

    default:
        http_response_code(400); 
        echo json_encode(['success' => false, 'error' => 'Invalid or missing action parameter.']);
        break;
}


$conn->close();
?>