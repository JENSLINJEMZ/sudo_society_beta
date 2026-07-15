<?php
require_once '../lib/includes/Database.class.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

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

$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id === null && !in_array($action, ['getLeaderboard'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required. Please log in.']);
    $conn->close();
    exit();
}

switch ($action) {

    case 'getUserStats':
        $sql = "SELECT username, total_score, challenges_solved, current_rank, time_spent_hours, daily_streak, last_solved_date FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getUserStats: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $lastSolvedDate = new DateTime($user['last_solved_date']);
            $today = new DateTime();
            $yesterday = new DateTime('yesterday');

            if ($lastSolvedDate->format('Y-m-d') < $yesterday->format('Y-m-d') && $lastSolvedDate->format('Y-m-d') != $today->format('Y-m-d')) {
                $user['daily_streak'] = 0;
            }

            $user['total_score'] = (int)$user['total_score'];
            $user['challenges_solved'] = (int)$user['challenges_solved'];
            $user['current_rank'] = (int)$user['current_rank'];
            $user['time_spent_hours'] = (float)$user['time_spent_hours'];
            $user['daily_streak'] = (int)$user['daily_streak'];

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
            case '30d':
                $time_interval = '30 DAY';
                break;

            case '90d':
                $time_interval = '90 DAY';
                break;

            case 'all':
                $time_interval = '10 YEAR';
                break;
        }

        $sql = "SELECT score, timestamp FROM score_history WHERE user_id = ? AND timestamp >= NOW() - INTERVAL " . $time_interval . " ORDER BY timestamp ASC";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getScoreProgression: ' . $conn->error]);
            exit();
        }

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
        $sql = "SELECT COALESCE(c.category, 'Unknown Category') AS category, COUNT(sc.id) as solved_count 
                FROM solved_challenges sc
                LEFT JOIN challenges c ON sc.challenges_id = c.id
                WHERE sc.user_id = ?
                GROUP BY COALESCE(c.category, 'Unknown Category')";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getChallengesByCategory: ' . $conn->error]);
            exit();
        }

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
        $sql = "SELECT COUNT(*) as total_challenges FROM challenges WHERE active = 1";
        $result = $conn->query($sql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch total challenges: ' . $conn->error]);
            exit();
        }

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

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getLatestAchievements: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $achievements = [];

        while ($row = $result->fetch_assoc()) {
            $row['progress_percent'] = ($row['total_required'] > 0)
                ? round(((float)$row['progress'] / (int)$row['total_required']) * 100)
                : 100;

            $row['total_required'] = (int)$row['total_required'];
            $row['progress'] = (int)$row['progress'];
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

        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement for getRecentActivity: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $activity = [];

        while ($row = $result->fetch_assoc()) {
            $row['points_change'] = (int)$row['points_change'];
            $activity[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $activity]);
        $stmt->close();
        break;

    case 'getLeaderboard':
        $sql = "SELECT current_rank AS rank, username, total_score AS score FROM users ORDER BY total_score DESC LIMIT 100";
        $result = $conn->query($sql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch leaderboard: ' . $conn->error]);
            exit();
        }

        $leaderboard = [];

        while ($row = $result->fetch_assoc()) {
            $row['rank'] = (int)$row['rank'];
            $row['score'] = (int)$row['score'];
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