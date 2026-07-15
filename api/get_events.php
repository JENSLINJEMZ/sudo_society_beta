<?php





header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type'); 


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); 
    exit();
}



$dbHost = 'localhost';      
$dbPort = '3306';           
$dbName = 'phpmyadmin';     
$dbUser = 'phpmyadmin';     
$dbPass = 'kali';           

try {
    
    
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    
    
    
    $stmt = $pdo->query("SELECT * FROM events ORDER BY countdown_date ASC");
    $allEvents = $stmt->fetchAll();

    $upcomingEvents = [];
    $pastEvents = [];
    $now = new DateTime(); 

    
    foreach ($allEvents as $event) {
        $eventCountdownDate = new DateTime($event['countdown_date']);
        
        
        $event['is_featured'] = (bool)$event['is_featured'];

        
        if ($eventCountdownDate > $now) {
            $event['status'] = 'upcoming';
            $upcomingEvents[] = $event;
        } else {
            $event['status'] = 'past';
            $pastEvents[] = $event;
        }
    }

    
    echo json_encode([
        'success' => true,
        'message' => 'Events fetched successfully.',
        'upcomingEvents' => $upcomingEvents,
        'pastEvents' => $pastEvents
    ]);

} catch (PDOException $e) {
    
    error_log("Database Error in get_events.php: " . $e->getMessage()); 
    http_response_code(500); 
    echo json_encode([
        'success' => false,
        'message' => 'Failed to connect to the database or fetch events. Please try again later.',
        
        
    ]);
} catch (Exception $e) {
    
    error_log("General Error in get_events.php: " . $e->getMessage()); 
    http_response_code(500); 
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected server error occurred.',
        
        
    ]);
}
?>