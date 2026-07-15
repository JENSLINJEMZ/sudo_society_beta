<?php
include 'Database.class.php';
include 'hash.class.php';
class User{
    private $conn = null;
    public $id;
    public $username;
    public $password;
    public $email;
    public $user;
    public function __construct(){
        $this->conn = DataBase::connection();
}
    public function register($username, $password, $email) {
    $this->conn = DataBase::connection();
    if (!$this->conn) return false;

    $pass = Hash::saltit($password);
    $stmt = $this->conn->prepare("INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $pass, $email);

    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Register failed: " . $stmt->error);
        return false;
    }
    }

    public function Login($username,$password){
        $conn = DataBase::connection();
        $query = "SELECT * FROM `users` WHERE username='$username';";
        $result = $conn->query($query);
        if($result->num_rows === 1){
            $row = $result->fetch_assoc();
            $verify = Hash::verify($password,$row['password']);
            if($verify == True){
                return $this->user = $row;
            }else{
                return false;
            }

        }
        else{
            return false;
        }
    }

}

$s= new User;
// $s->register("sample","passs","fa@gmail.com");
$s->Login("jemz","00000000");
