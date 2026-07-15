<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


session_start();


if (!isset($_SESSION['user_id'])) {
    http_response_code(401); 
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit();
}

$current_user_id = $_SESSION['user_id'];


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

$action = $_GET['action'] ?? '';

switch ($action) {
    
    case 'getTeams':
        try {
            $teams = [];
            $sql = "SELECT id, team_name, bio, image_url, description, leader_id FROM teams ORDER BY created_at DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                
                $leader_username = 'N/A';
                $leader_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $leader_stmt->bind_param("i", $row['leader_id']);
                $leader_stmt->execute();
                $leader_result = $leader_stmt->get_result();
                if ($leader_row = $leader_result->fetch_assoc()) {
                    $leader_username = $leader_row['username'];
                }
                $leader_stmt->close();
                
                
                $members_names = [];
                $members_stmt = $conn->prepare("SELECT id, username FROM users WHERE team_id = ? LIMIT 5"); 
                $members_stmt->bind_param("i", $row['id']);
                $members_stmt->execute();
                $members_result = $members_stmt->get_result();
                while($member_row = $members_result->fetch_assoc()) {
                    $members_names[] = ['id' => $member_row['id'], 'username' => $member_row['username']];
                }
                $members_stmt->close();
                
                
                $member_count = 0;
                $count_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE team_id = ?");
                $count_stmt->bind_param("i", $row['id']);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $member_count = $count_result->fetch_array()[0] ?? 0;
                $count_stmt->close();
                
                $row['leader_username'] = $leader_username;
                $row['member_count'] = $member_count;
                $row['members'] = $members_names;
                $teams[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $teams]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch teams: ' . $e->getMessage()]);
        }
        break;
        
    
    case 'getTeamDetails':
        if (!isset($_GET['team_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Team ID is required.']);
            exit();
        }
        $team_id = $_GET['team_id'];
        try {
            $team = null;
            $members = [];
            
            
            $stmt = $conn->prepare("SELECT id, team_name, bio, image_url, description, leader_id FROM teams WHERE id = ?");
            $stmt->bind_param("i", $team_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $team = $row;
            }
            $stmt->close();

            if ($team) {
                
                $leader_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $leader_stmt->bind_param("i", $team['leader_id']);
                $leader_stmt->execute();
                $leader_result = $leader_stmt->get_result();
                if ($leader_row = $leader_result->fetch_assoc()) {
                    $team['leader_username'] = $leader_row['username'];
                }
                $leader_stmt->close();

                
                $members_stmt = $conn->prepare("SELECT id, username, total_score, current_rank FROM users WHERE team_id = ?");
                $members_stmt->bind_param("i", $team_id);
                $members_stmt->execute();
                $members_result = $members_stmt->get_result();
                while ($member_row = $members_result->fetch_assoc()) {
                    $members[] = $member_row;
                }
                $members_stmt->close();

                $response_data = $team;
                $response_data['members'] = $members;
                echo json_encode(['success' => true, 'data' => $response_data]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Team not found.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch team details: ' . $e->getMessage()]);
        }
        break;
    
    
    case 'getUserDetails':
        if (!isset($_GET['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'User ID is required.']);
            exit();
        }
        $user_id = $_GET['user_id'];
        try {
            
            $stmt = $conn->prepare("SELECT username, total_score, current_rank FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $userDetails = $result->fetch_assoc();
                
                
                $solved_stmt = $conn->prepare("SELECT COUNT(*) AS solved_count FROM solved_challenges WHERE user_id = ?");
                $solved_stmt->bind_param("i", $user_id);
                $solved_stmt->execute();
                $solved_result = $solved_stmt->get_result();
                $solved_count = $solved_result->fetch_assoc()['solved_count'] ?? 0;
                $solved_stmt->close();

                $userDetails['solved_challenges_count'] = $solved_count;
                
                echo json_encode(['success' => true, 'data' => $userDetails]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'User not found.']);
            }
            $stmt->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch user details: ' . $e->getMessage()]);
        }
        break;


    
    case 'getUserTeam':
        try {
            $check_stmt = $conn->prepare("SELECT id, team_id FROM users WHERE id = ?");
            $check_stmt->bind_param("i", $current_user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $user_data = $check_result->fetch_assoc();
            $check_stmt->close();
            
            echo json_encode(['success' => true, 'data' => $user_data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to get user team status.']);
        }
        break;

    
    case 'createTeam':
        
        $check_stmt = $conn->prepare("SELECT team_id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $current_user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $user_data = $check_result->fetch_assoc();
        $check_stmt->close();

        if ($user_data['team_id'] !== NULL) {
            http_response_code(409); 
            echo json_encode(['success' => false, 'error' => 'You are already a member of a team. Please leave your current team to create a new one.']);
            exit();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $team_name = $input['team_name'] ?? '';
        $bio = $input['bio'] ?? '';
        $image_url = $input['image_url'] ?? 'https://placehold.co/100x100/0a0a0a/00ff88?text=Team';
        $description = $input['description'] ?? '';

        if (empty($team_name)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Team name is required.']);
            exit();
        }

        $conn->begin_transaction();

        try {
            
            $stmt = $conn->prepare("INSERT INTO teams (team_name, bio, image_url, description, leader_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $team_name, $bio, $image_url, $description, $current_user_id);
            $stmt->execute();
            $new_team_id = $conn->insert_id;
            $stmt->close();

            if ($new_team_id > 0) {
                
                $update_stmt = $conn->prepare("UPDATE users SET team_id = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_team_id, $current_user_id);
                $update_stmt->execute();
                $update_stmt->close();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Team created successfully.', 'team_id' => $new_team_id]);
            } else {
                $conn->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create team.']);
            }

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create team: ' . $e->getMessage()]);
        }
        break;
        
    
    case 'joinTeam':
        
        $check_stmt = $conn->prepare("SELECT team_id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $current_user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $user_data = $check_result->fetch_assoc();
        $check_stmt->close();

        if ($user_data['team_id'] !== NULL) {
            http_response_code(409); 
            echo json_encode(['success' => false, 'error' => 'You are already a member of a team. Please leave your current team first.']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $team_id = $input['team_id'] ?? '';

        if (empty($team_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Team ID is required.']);
            exit();
        }

        try {
            $stmt = $conn->prepare("UPDATE users SET team_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $team_id, $current_user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Joined team successfully.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Failed to join team. Team not found or user already a member.']);
            }
            $stmt->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to join team: ' . $e->getMessage()]);
        }
        break;

    
    case 'leaveTeam':
        
        $check_stmt = $conn->prepare("SELECT team_id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $current_user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $user_data = $check_result->fetch_assoc();
        $check_stmt->close();

        if ($user_data['team_id'] === NULL) {
            http_response_code(409); 
            echo json_encode(['success' => false, 'error' => 'You are not currently in a team.']);
            exit();
        }

        $conn->begin_transaction();
        try {
            
            $leader_check_stmt = $conn->prepare("SELECT id FROM teams WHERE id = ? AND leader_id = ?");
            $leader_check_stmt->bind_param("ii", $user_data['team_id'], $current_user_id);
            $leader_check_stmt->execute();
            $leader_result = $leader_check_stmt->get_result();
            $is_leader = $leader_result->num_rows > 0;
            $leader_check_stmt->close();

            if ($is_leader) {
                
                $member_count_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE team_id = ? AND id != ?");
                $member_count_stmt->bind_param("ii", $user_data['team_id'], $current_user_id);
                $member_count_stmt->execute();
                $member_count_result = $member_count_stmt->get_result();
                $member_count = $member_count_result->fetch_array()[0] ?? 0;
                $member_count_stmt->close();

                if ($member_count > 0) {
                    
                    $conn->rollback();
                    http_response_code(403); 
                    echo json_encode(['success' => false, 'error' => 'You are the team leader. You must promote another member before leaving.']);
                    exit();
                } else {
                    
                    $delete_team_stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
                    $delete_team_stmt->bind_param("i", $user_data['team_id']);
                    $delete_team_stmt->execute();
                    $delete_team_stmt->close();

                    
                    $conn->commit();
                    echo json_encode(['success' => true, 'message' => 'Team disbanded and you have left successfully.']);
                    exit();
                }
            }
            
            
            $stmt = $conn->prepare("UPDATE users SET team_id = NULL WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Left team successfully.']);
            } else {
                $conn->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to leave team.']);
            }
            $stmt->close();

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to leave team: ' . $e->getMessage()]);
        }
        break;
        
    
    case 'promoteLeader':
        $input = json_decode(file_get_contents('php://input'), true);
        $team_id = $input['team_id'] ?? '';
        $new_leader_id = $input['new_leader_id'] ?? '';

        if (empty($team_id) || empty($new_leader_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Team ID and New Leader ID are required.']);
            exit();
        }

        $conn->begin_transaction();

        try {
            
            $check_leader_stmt = $conn->prepare("SELECT id FROM teams WHERE id = ? AND leader_id = ?");
            $check_leader_stmt->bind_param("ii", $team_id, $current_user_id);
            $check_leader_stmt->execute();
            $leader_result = $check_leader_stmt->get_result();
            if ($leader_result->num_rows == 0) {
                $conn->rollback();
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'You are not the leader of this team.']);
                exit();
            }
            $check_leader_stmt->close();
            
            
            $check_member_stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND team_id = ?");
            $check_member_stmt->bind_param("ii", $new_leader_id, $team_id);
            $check_member_stmt->execute();
            if ($check_member_stmt->get_result()->num_rows == 0) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'The selected member is not in the team.']);
                exit();
            }
            $check_member_stmt->close();

            
            $update_team_stmt = $conn->prepare("UPDATE teams SET leader_id = ? WHERE id = ?");
            $update_team_stmt->bind_param("ii", $new_leader_id, $team_id);
            $update_team_stmt->execute();
            $update_team_stmt->close();
            
            
            $leave_team_stmt = $conn->prepare("UPDATE users SET team_id = NULL WHERE id = ?");
            $leave_team_stmt->bind_param("i", $current_user_id);
            $leave_team_stmt->execute();
            $leave_team_stmt->close();
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'New leader promoted and you have successfully left the team.']);

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to promote new leader: ' . $e->getMessage()]);
        }
        break;


    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}

$conn->close();
?>