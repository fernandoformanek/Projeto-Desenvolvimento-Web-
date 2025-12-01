<?php
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$login = false;
$user_id = null;
$user_name = null;
$user_email = null;
$user_current_league_id = null;    
$user_current_league_name = null;  

  if (isset($_SESSION["user_id"]) && isset($_SESSION["user_name"]) && isset($_SESSION["user_email"])) {
    $login = true;
    $user_id = $_SESSION["user_id"];
    $user_name = $_SESSION["user_name"];
    $user_email = $_SESSION["user_email"];
     $user_current_league_id = $_SESSION["user_current_league_id"] ?? null; 
    $user_current_league_name = $_SESSION["user_current_league_name"] ?? null; 
  }


?>
