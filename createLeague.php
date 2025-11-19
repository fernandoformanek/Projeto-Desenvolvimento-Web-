<?php
require "db_functions.php";
require "authenticate.php"; // Incluir para verificar o login

if (!$login) {
    // Vai para o login se não estiver logado
    header("Location: login.php");
    exit;
}

$error = false;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["name"]) && isset($_POST["join_key"])) {
        $conn = connect_db();
        $name = mysqli_real_escape_string($conn, $_POST["name"]);
        $join_key = mysqli_real_escape_string($conn, $_POST["join_key"]);
        $creator_id = $user_id; // ID do usuário logado

        // cria a Liga
        $sql_league = "INSERT INTO Leagues
                       (name, creator_id, join_key, created_at) VALUES
                       ('$name', '$creator_id', '$join_key', NOW());";

        if (mysqli_query($conn, $sql_league)) {
            $league_id = mysqli_insert_id($conn);

            // adiciona o criador como membro
            $sql_membership = "INSERT INTO LeagueMemberships
                               (user_id, league_id, joined_at) VALUES
                               ('$creator_id', '$league_id', NOW());";

            if (mysqli_query($conn, $sql_membership)) {
                $success = true;
            } else {
                $error_msg = "Erro ao adicionar membro: " . mysqli_error($conn);
                $error = true;
            }

        } else {
            $error_msg = "Erro ao criar liga: " . mysqli_error($conn);
            $error = true;
        }

        disconnect_db($conn);
    } else {
        $error_msg = "Preencha todos os campos.";
        $error = true;
    }
}
?>