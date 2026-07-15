<?php
require_once '../lib/includes/Database.class.php';
session_start(); 


header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');



$conn = DataBase::connection();


if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}




if (!isset($_SESSION['user_id'])) {
    
    
    $_SESSION['user_id'] = 1; 
}
$user_id = $_SESSION['user_id'];


$action = isset($_GET['action']) ? $_GET['action'] : '';


switch ($action) {

    
    case 'getTasksList':
        $tasks = [];
        $sql = "SELECT id, title, type FROM ctf_tasks ORDER BY id ASC"; 
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $task_id = $row['id'];
                
                
                $total_questions_sql = "SELECT JSON_LENGTH(questions) AS total_q FROM ctf_tasks WHERE id = ?";
                $stmt_total_q = $conn->prepare($total_questions_sql);
                $stmt_total_q->bind_param('s', $task_id);
                $stmt_total_q->execute();
                $total_q_result = $stmt_total_q->get_result();
                $total_q_row = $total_q_result->fetch_assoc();
                $total_questions = $total_q_row ? (int)$total_q_row['total_q'] : 0;
                $stmt_total_q->close();

                $solved_questions_sql = "SELECT COUNT(DISTINCT question_id) AS solved_q FROM solved_challenges WHERE user_id = ? AND ctf_task_id = ?";
                $stmt_solved_q = $conn->prepare($solved_questions_sql);
                $stmt_solved_q->bind_param('is', $user_id, $task_id);
                $stmt_solved_q->execute();
                $solved_q_result = $stmt_solved_q->get_result();
                $solved_q_row = $solved_q_result->fetch_assoc();
                $solved_questions = $solved_q_row ? (int)$solved_q_row['solved_q'] : 0;
                $stmt_solved_q->close();

                $row['completed'] = ($total_questions > 0 && $solved_questions === $total_questions);
                $tasks[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $tasks]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch tasks.']);
        }
        break;

    
    case 'getTaskDetails':
        $task_id = isset($_GET['taskId']) ? $_GET['taskId'] : '';
        if (empty($task_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Task ID is required.']);
            exit();
        }

        $sql = "SELECT id, title, type, story, learning_content, resources, questions FROM ctf_tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $task_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($task = $result->fetch_assoc()) {
            
            $task['resources'] = json_decode($task['resources'], true);
            $task['questions'] = json_decode($task['questions'], true);

            
            if (!empty($task['questions'])) {
                foreach ($task['questions'] as $key => $question) {
                    $q_id = $question['id'];
                    $check_solved_sql = "SELECT 1 FROM solved_challenges WHERE user_id = ? AND ctf_task_id = ? AND question_id = ?";
                    $stmt_check = $conn->prepare($check_solved_sql);
                    $stmt_check->bind_param('iss', $user_id, $task_id, $q_id);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    $task['questions'][$key]['solved'] = $stmt_check->num_rows > 0;
                    $stmt_check->close();
                }
            }

            echo json_encode(['success' => true, 'data' => $task]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Task not found.']);
        }
        $stmt->close();
        break;

    
    case 'submitFlag':
        $input = json_decode(file_get_contents('php://input'), true);
        $task_id = $input['taskId'] ?? '';
        $question_id = $input['questionId'] ?? '';
        $submitted_answer = $input['submittedAnswer'] ?? '';

        if (empty($task_id) || empty($question_id) || !isset($submitted_answer)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing parameters.']);
            exit();
        }

        
        $sql_get_q = "SELECT questions FROM ctf_tasks WHERE id = ?";
        $stmt_get_q = $conn->prepare($sql_get_q);
        $stmt_get_q->bind_param('s', $task_id);
        $stmt_get_q->execute();
        $result_get_q = $stmt_get_q->get_result();
        $task_row = $result_get_q->fetch_assoc();
        $stmt_get_q->close();

        if (!$task_row) {
            echo json_encode(['success' => false, 'message' => 'Task not found.']);
            exit();
        }

        $questions_json = json_decode($task_row['questions'], true);
        $correct_answer = null;
        $question_points = 50; 

        foreach ($questions_json as $q) {
            if ($q['id'] === $question_id) {
                $correct_answer = $q['answer'];
                break;
            }
        }

        if ($correct_answer === null) {
            echo json_encode(['success' => false, 'message' => 'Question not found for this task.']);
            exit();
        }

        
        $check_solved_sql = "SELECT 1 FROM solved_challenges WHERE user_id = ? AND ctf_task_id = ? AND question_id = ?";
        $stmt_check_solved = $conn->prepare($check_solved_sql);
        $stmt_check_solved->bind_param('iss', $user_id, $task_id, $question_id);
        $stmt_check_solved->execute();
        $stmt_check_solved->store_result();
        if ($stmt_check_solved->num_rows > 0) {
            echo json_encode(['success' => true, 'correct' => true, 'message' => 'Already solved!']);
            $stmt_check_solved->close();
            exit();
        }
        $stmt_check_solved->close();

        
        if (strtolower($submitted_answer) === strtolower($correct_answer)) {
            
            $insert_solved_sql = "INSERT INTO solved_challenges (user_id, ctf_task_id, question_id, timestamp) VALUES (?, ?, ?, NOW())";
            $stmt_insert_solved = $conn->prepare($insert_solved_sql);
            $stmt_insert_solved->bind_param('iss', $user_id, $task_id, $question_id);
            $stmt_insert_solved->execute();
            $stmt_insert_solved->close();

            
            $update_user_sql = "UPDATE users SET total_score = total_score + ?, challenges_solved = challenges_solved + 1 WHERE id = ?";
            $stmt_update_user = $conn->prepare($update_user_sql);
            $stmt_update_user->bind_param('ii', $question_points, $user_id);
            $stmt_update_user->execute();
            $stmt_update_user->close();

            
            $log_description = "Solved question '{$question_id}' for task '{$task_id}'";
            $insert_log_sql = "INSERT INTO activity_log (user_id, activity_type, description, points_change, timestamp) VALUES (?, 'solved_question', ?, ?, NOW())";
            $stmt_insert_log = $conn->prepare($insert_log_sql);
            $stmt_insert_log->bind_param('isi', $user_id, $log_description, $question_points);
            $stmt_insert_log->execute();
            $stmt_insert_log->close();

            echo json_encode(['success' => true, 'correct' => true, 'message' => 'Correct Answer!']);
        } else {
            echo json_encode(['success' => true, 'correct' => false, 'message' => 'Incorrect. Try again!']);
        }
        break;

    
    
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

    case 'getTotalChallenges':
        $sql = "SELECT COUNT(*) as total_challenges FROM ctf_tasks"; 
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => (int)$row['total_challenges']]);
        break;

    
    

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing action parameter.']);
        break;
}


$conn->close();
?>