<?php
require 'db_credentials.php';

// Create connection
$conn = mysqli_connect($servername, $username, $db_password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check connection
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

// criar tabela users
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
  UNIQUE (email)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Table created successfully<br>";
} else {
    echo "<br>Error creating database: " . mysqli_error($conn);
}

// Tabela: leagues (Ligas)
$sql = "CREATE TABLE leagues (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  keyword VARCHAR(255) NOT NULL, -- Para armazenar a palavra-chave hash da liga
  creator_id INT(6) UNSIGNED NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table 'leagues' created successfully<br>";
} else {
    echo "<br>Error creating table 'leagues': " . mysqli_error($conn);
}

// Tabela: user_leagues (Associação N:N entre Usuários e Ligas)
$sql = "CREATE TABLE user_leagues (
  user_id INT(6) UNSIGNED NOT NULL,
  league_id INT(6) UNSIGNED NOT NULL,
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, league_id), -- Chave primária composta
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table 'user_leagues' created successfully<br>";
} else {
    echo "<br>Error creating table 'user_leagues': " . mysqli_error($conn);
}

// Tabela: match_history 
$sql = "CREATE TABLE match_history (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT(6) UNSIGNED NOT NULL,
  league_id INT(6) UNSIGNED NULL, -- Pode ser NULL se a partida não for de uma liga específica
  score_gained INT NOT NULL,
  played_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE SET NULL
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table 'match_history' created successfully<br>";
} else {
    echo "<br>Error creating table 'match_history': " . mysqli_error($conn);
}



mysqli_close($conn)
?>
