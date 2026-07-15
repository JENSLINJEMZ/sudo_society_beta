<?php



ini_set('display_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');


session_start();


$host = 'sql12.freesqldatabase.com';
$dbname = 'sql12790354';
$username = 'sql12790354';
$password = '1zUXG3x4QX';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}


$action = $_GET['action'] ?? '';


function jsonResponse($data, $success = true) {
    echo json_encode(['success' => $success, 'data' => $data]);
    exit;
}


function errorResponse($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}


if ($action === 'getChallenges') {
    try {
        $sql = "SELECT 
                    id,
                    name AS title,
                    category,
                    points,
                    flag,
                    solves,
                    active,
                    created_at,
                    day_label,
                    story,
                    learning_objectives_json,
                    learning_details_html,
                    resources_json,
                    questions_json,
                    machine_link
                FROM challenges 
                WHERE active = 1
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $challenges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        foreach ($challenges as &$challenge) {
            $challenge['solved'] = false; 
            $challenge['learning_objectives'] = json_decode($challenge['learning_objectives_json'], true);
            $challenge['resources'] = json_decode($challenge['resources_json'], true);
            $challenge['questions'] = json_decode($challenge['questions_json'], true);
        }

        jsonResponse($challenges);

    } catch (Exception $e) {
        errorResponse("Error fetching challenges: " . $e->getMessage());
    }
}

errorResponse('Invalid or missing action');
