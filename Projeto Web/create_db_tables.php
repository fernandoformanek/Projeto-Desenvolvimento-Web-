<?php
require 'db_credentials.php';

$conn = mysqli_connect($servername, $username, $db_password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE $dbname";
if (mysqli_query($conn, $sql)) {
    echo "<br>Database created successfully<br>";
} else {
    echo "<br>Error creating database: " . mysqli_error($conn);
}

// Choose database
$sql = "USE $dbname";
if (mysqli_query($conn, $sql)) {
    echo "<br>Database changed successfully<br>";
} else {
    echo "<br>Error changing database: " . mysqli_error($conn);
}

// Criar tabela users
$sql = "CREATE TABLE $table_users (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(128) NOT NULL,
  total_score INT DEFAULT 0, 
  created_at DATETIME,
  updated_at DATETIME,
  last_login_at DATETIME,
  last_logout_at DATETIME,
  current_league_id INT UNSIGNED NULL DEFAULT NULL, 
  UNIQUE (email)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Table $table_users created successfully<br>"; 
} else {
    echo "<br>Error creating table $table_users: " . mysqli_error($conn); 
}

// Criar tabela leagues 
$sql = "CREATE TABLE $table_leagues (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  keyword VARCHAR(255) NOT NULL, -- Para armazenar a palavra chave hash da liga
  creator_id INT(6) UNSIGNED NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES $table_users(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table '$table_leagues' created successfully<br>";
} else {
    echo "<br>Error creating table '$table_leagues': " . mysqli_error($conn);
}

// Criar tabela match_history 
$sql = "CREATE TABLE $table_match_history (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT(6) UNSIGNED NOT NULL,
  league_id INT(6) UNSIGNED NULL DEFAULT NULL,
  score_gained INT NOT NULL,
  played_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES $table_users(id) ON DELETE CASCADE,
  FOREIGN KEY (league_id) REFERENCES $table_leagues(id) ON DELETE SET NULL
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table '$table_match_history' created successfully<br>";
} else {
    echo "<br>Error creating table '$table_match_history': " . mysqli_error($conn);
}


$sql = "ALTER TABLE $table_users
        ADD CONSTRAINT fk_current_league
        FOREIGN KEY (current_league_id) REFERENCES $table_leagues(id) ON DELETE SET NULL";
if (mysqli_query($conn, $sql)) {
    echo "<br>Foreign key 'fk_current_league' added to $table_users successfully<br>";
} else {
    echo "<br>Error adding foreign key to $table_users: " . mysqli_error($conn);
}


mysqli_close($conn);
?>