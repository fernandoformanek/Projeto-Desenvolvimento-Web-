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

// sql to create table(users)
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

//pontuação semanal e data de reset semanal
ALTER TABLE Users
ADD COLUMN weekly_score INT DEFAULT 0,
ADD COLUMN weekly_reset_at DATETIME DEFAULT CURRENT_TIMESTAMP;

if (mysqli_query($conn, $sql)) {
    echo "<br>Table created successfully<br>";
} else {
    echo "<br>Error creating database: " . mysqli_error($conn);
}

mysqli_close($conn)
?>

// sql to create table(Leagues)
$sql = "CREATE TABLE Leagues (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  creator_id INT(6) UNSIGNED,
  join_key VARCHAR(100) NOT NULL, 
  created_at DATETIME,
  UNIQUE (name),
  FOREIGN KEY (creator_id) REFERENCES Users(id)
);";


// sql to create table(LeagueMemberships)(ligação entre os usuários e as ligas.)
  CREATE TABLE LeagueMemberships (
  user_id INT(6) UNSIGNED,
  league_id INT(6) UNSIGNED,
  joined_at DATETIME,
  PRIMARY KEY (user_id, league_id), 
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (league_id) REFERENCES Leagues(id)
);