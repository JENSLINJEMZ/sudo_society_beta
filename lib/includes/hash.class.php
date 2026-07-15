<?php

class Hash{
    public static function saltit($password){
        $options = [
        'cost' => 8,
        ];
        $pass = password_hash("$password",PASSWORD_BCRYPT,$options);
        return $pass;
        
    }
    public static function verify($passwoed,$hash){
        return password_verify($passwoed,$hash);
    }
}
$pass = $_GET['pass'];
echo Hash::saltit("$pass");