<?php 
include 'includes/Database.class.php';
global $__site_config;
$__site_config = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../dbconfig.json');
function get_config($key,$default=null){
    global $__site_config;
    $array = json_decode($__site_config,true);
    if(isset($array[$key])){
        return $array[$key];
    }
    else{
        return $default;
    }
}