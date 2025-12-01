<?php
require "authenticate.php"; 
require "db_functions.php"; 
require "db_credentials.php"; 

// --- INÍCIO DA LÓGICA PARA SALVAR PONTUAÇÃO ---
$score_save_message = ''; // Variável para armazenar mensagens de sucesso/erro

// Verifica se a requisição é um POST para salvar pontuação E se o usuário está logado
if ($login && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "save_score") {
    $user_id = $_SESSION["user_id"];
    // Garante que a pontuação seja um número inteiro e positivo
    $score_to_add = isset($_POST["final_score"]) ? (int)$_POST["final_score"] : 0;

    $current_league_id = $user_current_league_id; 

    if ($score_to_add > 0) {
        $conn = connect_db();

        $score_to_add_escaped = mysqli_real_escape_string($conn, $score_to_add);
        $user_id_escaped = mysqli_real_escape_string($conn, $user_id);

        $league_id_escaped = ($current_league_id !== null) ? "'" . mysqli_real_escape_string($conn, $current_league_id) . "'" : "NULL";

        $sql_update_user = "UPDATE $table_users 
        SET total_score = total_score + '$score_to_add_escaped' 
        WHERE id = '$user_id_escaped'";

        if(mysqli_query($conn, $sql_update_user)){
            $sql_insert_match = "INSERT INTO $table_match_history (user_id, league_id, score_gained) 
            VALUES ('$user_id_escaped', $league_id_escaped, '$score_to_add_escaped')";

            if (mysqli_query($conn, $sql_insert_match)) {
                    $league_info = ($current_league_id !== null) ? " à liga <strong>" . htmlspecialchars($user_current_league_name) . "</strong>" : "";
                    $score_save_message = "<p style='color: green;'>Pontuação adicionada com sucesso ao seu perfil$league_info!</p>";
            } else {
                $score_save_message = "<p style='color: red;'>Erro ao registrar partida no histórico: " . mysqli_error($conn) . "</p>";
            }
        } else {
            $score_save_message = "<p style='color: red;'>Erro ao salvar pontuação: " . mysqli_error($conn) . "</p>";
        }
        disconnect_db($conn);
    } else {
        $score_save_message = "<p style='color: orange;'>Pontuação zero ou inválida. Nada foi salvo.</p>";
    }
}

// --- FIM DA LÓGICA PARA SALVAR PONTUAÇÃO ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida JustType</title>
    <link rel="stylesheet" href="jogo.css">
</head>
<body>
    <p id ="timer">30</p>
    <header>
        <nav>
            <a href="jogo.php">Jogar</a>
            <a href="liga.php">Ligas</a>
            <a href="<?php echo $login ? 'perfil.php' : 'login.php'; ?>">
                <?php echo $login ? 'Perfil (' . htmlspecialchars($_SESSION["user_name"]) . ')' : 'Login'; ?>
            </a>
            <?php if ($login): ?>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        </nav>
    </header>

    <h1 id="titulo">JustType</h1>
    <section id="secao-jogo">
    <p id="frase"><h4></h4></p>

    <div id="letras-jogo">

    </div>

    <p id="pontuacao">Pontuação: <span id="pontos">0</span></p>
    <input type="text" id="input-usuario" placeholder="Digite aqui..." autocomplete="off"> 
    <button id="botao-iniciar">Iniciar Jogo</button>

    <?php if ($login): // O formulário só aparece se o usuário estiver logado ?>
    <form id="scoreForm" action="jogo.php" method="post" style="display:none;">
        <input type="hidden" name="action" value="save_score">
        <input type="hidden" name="final_score" id="final_score_input">
    </form>
    <?php endif; ?>

     <?php if (!empty($score_save_message)): ?>
        <div class="message">
            <?php echo $score_save_message; ?>
        </div>
    <?php endif; ?>


    <script src="jogo.js"></script>
    </section>
</body>
</html>