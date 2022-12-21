<?php
$serverName = "localhost";
$userName = "root";
$userPassword = "";
$bdName = "pizzeria";

if(empty($userName)){
    echo "Add database data";
}else{
    $conn = mysqli_connect($serverName, $userName, $userPassword, $bdName);
    if(!$conn){
        die("Login error ". mysqli_connect_error());
    }
}
?>
