<?php
require 'db_credentials.php';

$conn = mysqli_connect($servername, $username, $db_password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// criar banco de dados
$sql = "CREATE DATABASE $dbname";
if (mysqli_query($conn, $sql)) {
    echo "<br>Database created successfully<br>";
} else {
    echo "<br>Error creating database: " . mysqli_error($conn);
}

// Selecionar o banco de dados
$sql = "USE $dbname";
if (mysqli_query($conn, $sql)) {
    echo "<br>Database changed successfully<br>";
} else {
    echo "<br>Error changing database: " . mysqli_error($conn);
}

// Criar tabela users
$sql = "CREATE TABLE $table_users (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- id
  name VARCHAR(100) NOT NULL, -- nome
  email VARCHAR(100) NOT NULL, -- email
  password VARCHAR(128) NOT NULL, -- senha
  total_score INT DEFAULT 0, -- pontuação total
  current_league_id INT UNSIGNED NULL DEFAULT NULL, -- liga atual 
  UNIQUE (email) 
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Table $table_users created successfully<br>"; 
} else {
    echo "<br>Error creating table $table_users: " . mysqli_error($conn); 
}

// Criar tabela leagues
// Referencia users 
$sql = "CREATE TABLE $table_leagues (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- id
  name VARCHAR(100) NOT NULL UNIQUE, -- nome
  keyword VARCHAR(255) NOT NULL, -- palavra chave hash da liga
  creator_id INT(6) UNSIGNED NOT NULL, -- id do criador da liga
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- quando foi criada a liga
  FOREIGN KEY (creator_id) REFERENCES $table_users(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Table '$table_leagues' created successfully<br>";
} else {
    echo "<br>Error creating table '$table_leagues': " . mysqli_error($conn);
}

// Criar tabela match_history 
// Referencia users e leagues
$sql = "CREATE TABLE $table_match_history (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- id
  user_id INT(6) UNSIGNED NOT NULL, -- id do usuario
  league_id INT(6) UNSIGNED NULL DEFAULT NULL, -- id da liga
  score_gained INT NOT NULL, -- quantos pontos ganhou
  played_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- data que jogou a partida
  FOREIGN KEY (user_id) REFERENCES $table_users(id) ON DELETE CASCADE, -- todas as partidas serão deletadas se um usuario for deletado
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