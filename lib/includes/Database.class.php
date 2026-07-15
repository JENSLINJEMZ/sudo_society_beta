<?php 

class DataBase{
    public static function connection(){
        $db_host = getenv('DB_HOST') ?: 'localhost';
        $db_port = getenv('DB_PORT') ?: '3306';
        $db_user = getenv('DB_USER') ?: 'ArchLinux'; 
        $db_pass = getenv('DB_PASS') ?: 'arch';
        $db_name = getenv('DB_NAME') ?: 'phpmyadmin'; 
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
        return $conn;
                
    }
}

