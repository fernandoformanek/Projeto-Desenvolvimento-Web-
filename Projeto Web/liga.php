<?php
require "authenticate.php";
require "db_functions.php";
require "db_credentials.php";

// Garante que o usuário esteja logado para acessar esta página
if (!$login) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
// Variáveis da liga ativa já vem do authenticate.php

$conn = connect_db(); // Abre a conexão com o banco de dados

$user_id_escaped = mysqli_real_escape_string($conn, $user_id); 

$message = '';

// --- CRIAR NOVA LIGA ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create_league") {
    
    // pega nome e palavra chave do formulario e remove espaços
    $league_name = trim($_POST["league_name"]);
    $league_keyword = trim($_POST["league_keyword"]);


    // Escape string previne SQL injection
    $league_name_escaped = mysqli_real_escape_string($conn, $league_name);
    $hashed_keyword = md5($league_keyword); // A palavra chave sera armazenada como hash MD5
    $hashed_keyword_escaped = mysqli_real_escape_string($conn, $hashed_keyword);
    $user_id_escaped = mysqli_real_escape_string($conn, $user_id);

    // verificacoes para criação da liga
     if (empty($league_name) || empty($league_keyword)) {
        $message = "<p style='color: red;'>Nome e palavra-chave da liga são obrigatórios.</p>";
    } else {
        // Verifica se o nome da liga já existe
        $sql_check_name = "SELECT id FROM $table_leagues WHERE name = '$league_name_escaped'";
        $result_check_name = mysqli_query($conn, $sql_check_name);

        if ($result_check_name && mysqli_num_rows($result_check_name) > 0) {
            $message = "<p style='color: orange;'>Já existe uma liga com este nome</p>";
        } else {
            // Insere a nova liga
            $sql_insert_league = "INSERT INTO $table_leagues (name, keyword, creator_id)
            VALUES ('$league_name_escaped', '$hashed_keyword_escaped', '$user_id_escaped')";

            if (mysqli_query($conn, $sql_insert_league)) {
                $new_league_id = mysqli_insert_id($conn); 
                $new_league_id_escaped = mysqli_real_escape_string($conn, $new_league_id);
                
                // atualiza o bd definindo uma nova liga ativa
                $sql_set_active_league = "UPDATE $table_users SET current_league_id = '$new_league_id_escaped' WHERE id = '$user_id_escaped'";
                if (mysqli_query($conn, $sql_set_active_league)) {

                    // Atualiza a sessão para a nova liga ativa
                    $_SESSION["user_current_league_id"] = $new_league_id;
                    $_SESSION["user_current_league_name"] = htmlspecialchars($league_name); //htmlspecialchars previne que o usuario modifique algo na pagina
                    $user_current_league_id = $new_league_id; // Atualiza variável 
                    $user_current_league_name = htmlspecialchars($league_name); // Atualiza variável 
                    $message = "<p style='color: green;'>Liga '<strong>" . htmlspecialchars($league_name) . "</strong>' criada com sucesso e você já faz parte dela como sua liga ativa! </p>";
                } else {
                    $message = "<p style='color: red;'>Liga criada, mas erro ao defini-la como sua liga ativa: " . mysqli_error($conn) . " </p>";
                }
            } else {
                $message = "<p style='color: red;'>Erro ao criar liga: " . mysqli_error($conn) . " </p>";
            }
        }
    }
}

// --- ENTRAR EM LIGA EXISTENTE ---
// se o formulario de Participar de Liga Existente for enviado:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "join_league") {

    // obtem os dados enviados no formulario de entrar em liga
    $league_id_to_join = (int)$_POST["league_id"];
    $entered_keyword = trim($_POST["join_keyword"]);

    // ESCAPAR TODOS OS INPUTS DO USUÁRIO
    $league_id_to_join_escaped = mysqli_real_escape_string($conn, $league_id_to_join);
    $entered_keyword_escaped = mysqli_real_escape_string($conn, $entered_keyword); 
    $user_id_escaped = mysqli_real_escape_string($conn, $user_id);

    // Validação
      if (empty($entered_keyword) || $league_id_to_join <= 0) {
        $message = "<p style='color: red;'>Selecione uma liga e forneça a palavra-chave. </p>";
    } else {

        // VERIFICAR SE O USUÁRIO JÁ TEM UMA LIGA ATIVA
        if ($user_current_league_id !== null) {
            $message = "<p style='color: orange;'>Você já está em uma liga ativa (<strong>" . htmlspecialchars($user_current_league_name) . "</strong>). Saia dela antes de entrar em outra.</p>";
        } else {

             // Busca a palavra chave da liga
            $sql_get_league_keyword = "SELECT keyword, name FROM $table_leagues WHERE id = '$league_id_to_join_escaped'";
            $result_get_league_keyword = mysqli_query($conn, $sql_get_league_keyword);
            $league_data = mysqli_fetch_assoc($result_get_league_keyword); // organiza os dados da liga em um array

            // se o usuario digitou a senha correta
             if ($league_data && md5($entered_keyword_escaped) === $league_data['keyword']) {

                // DEFINIR A NOVA LIGA COMO A LIGA ATIVA DO USUÁRIO
                $sql_set_active_league = "UPDATE $table_users SET current_league_id = '$league_id_to_join_escaped' WHERE id = '$user_id_escaped'";
                
                if (mysqli_query($conn, $sql_set_active_league)) {
                    // Atualiza a sessão
                    $_SESSION["user_current_league_id"] = $league_id_to_join;
                    $_SESSION["user_current_league_name"] = htmlspecialchars($league_data['name']);
                    $user_current_league_id = $league_id_to_join; // Atualiza variável 
                    $user_current_league_name = htmlspecialchars($league_data['name']); // Atualiza variável 
                    $message = "<p style='color: green;'>Você entrou na liga '<strong>" . htmlspecialchars($league_data['name']) . "</strong>' com sucesso! Ela é agora sua liga ativa. </p>";
                } else {
                    $message = "<p style='color: red;'>Erro ao entrar na liga (não foi possível defini-la como ativa): " . mysqli_error($conn) . " </p>";
                }
            } else {
                $message = "<p style='color: red;'>Palavra-chave incorreta para esta liga. </p>";
            }
        }
    }
}

// --- SAIR DA LIGA ATIVA ---
// se o formulario sair da liga for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "leave_league") {
    
    if ($user_current_league_id !== null) {
        $user_id_escaped = mysqli_real_escape_string($conn, $user_id);
        // Define current_league_id como NULL na tabela users
        $sql_leave_league = "UPDATE $table_users SET current_league_id = NULL WHERE id = '$user_id_escaped'";
        if (mysqli_query($conn, $sql_leave_league)) {
            // Remove a liga ativa da sessão
            $old_league_name = $_SESSION["user_current_league_name"];
            $_SESSION["user_current_league_id"] = null;
            $_SESSION["user_current_league_name"] = null;
            $user_current_league_id = null; // Atualiza variável 
            $user_current_league_name = null; // Atualiza variável
            $message = "<p style='color: green;'>Você saiu da liga '<strong>" . htmlspecialchars($old_league_name) . "</strong>' com sucesso. </p>";
        } else {
            $message = "<p style='color: red;'>Erro ao sair da liga: " . mysqli_error($conn) . " </p>";
        }
    } else {
        $message = "<p style='color: orange;'>Você não está em nenhuma liga ativa para sair.</p>";
    }
}


// --- EXIBIR DADOS ---

// Todas as ligas disponiveis para o usuário entrar
$all_leagues = [];
$sql_all_leagues = "SELECT id, name FROM $table_leagues ORDER BY name ASC";
$result_all_leagues = mysqli_query($conn, $sql_all_leagues);

if ($result_all_leagues) {
    while ($row = mysqli_fetch_assoc($result_all_leagues)) { // transforma as linhas em array
        $all_leagues[] = $row; // add linha na lista
    }
}

// --- RANKING GLOBAL ---
$global_ranking_total = [];
// lista os usuarios ordenando pela pontuação
$sql_global_total = "SELECT name, total_score FROM $table_users ORDER BY total_score DESC, name ASC";
$result_global_total = mysqli_query($conn, $sql_global_total);

if ($result_global_total) {
    while ($row = mysqli_fetch_assoc($result_global_total)) { // transforma as linhas em array
        $global_ranking_total[] = $row; // add linha na lista
    }
}

// --- RANKING GLOBAL SEMANAL---
$global_ranking_weekly = [];
$start_of_week = date('Y-m-d H:i:s', strtotime('last sunday 00:00:00'));
$start_of_week_escaped = mysqli_real_escape_string($conn, $start_of_week);

$sql_global_weekly = "
    SELECT u.name, SUM(mh.score_gained) as weekly_score
    FROM $table_users u
    JOIN $table_match_history mh ON u.id = mh.user_id
    WHERE mh.played_at >= '$start_of_week_escaped'
    GROUP BY u.id, u.name
    ORDER BY weekly_score DESC, u.name ASC";
$result_global_weekly = mysqli_query($conn, $sql_global_weekly);

if ($result_global_weekly) {
    while ($row = mysqli_fetch_assoc($result_global_weekly)) { // transforma as linhas em array
        $global_ranking_weekly[] = $row; // add linha na lista
    }
}

// --- RANKINGS POR LIGA ---
$league_rankings = [];
// só processa os rankings da liga ativa do usuário, se houver
if ($user_current_league_id !== null) {
    $league_id = $user_current_league_id;
    $league_name = $user_current_league_name;
    $league_id_escaped = mysqli_real_escape_string($conn, $league_id);

    // Ranking Total da Liga
    $league_ranking_total = [];
    $sql_league_total = "
        SELECT u.name, SUM(mh.score_gained) as league_total_score
        FROM $table_users u
        JOIN $table_match_history mh ON u.id = mh.user_id
        WHERE mh.league_id = '$league_id_escaped'
        GROUP BY u.id, u.name
        ORDER BY league_total_score DESC, u.name ASC";
    $result_league_total = mysqli_query($conn, $sql_league_total);

    if ($result_league_total) {
        while ($row = mysqli_fetch_assoc($result_league_total)) { // transforma as linhas em array
            $league_ranking_total[] = $row; // add linha na lista
        }
    }

    // Ranking Semanal da Liga
    $league_ranking_weekly = [];
    $sql_league_weekly = "
        SELECT u.name, SUM(mh.score_gained) as league_weekly_score
        FROM $table_users u
        JOIN $table_match_history mh ON u.id = mh.user_id
        WHERE mh.league_id = '$league_id_escaped' AND mh.played_at >= '$start_of_week_escaped'
        GROUP BY u.id, u.name
        ORDER BY league_weekly_score DESC, u.name ASC";
    $result_league_weekly = mysqli_query($conn, $sql_league_weekly);

    if ($result_league_weekly) {
        while ($row = mysqli_fetch_assoc($result_league_weekly)) { // transforma as linhas em array
            $league_ranking_weekly[] = $row; // add linha na lista
        }
    }

    // armazena os rankings em array 
    $league_rankings[$league_id] = [
        'name' => $league_name,
        'total' => $league_ranking_total,
        'weekly' => $league_ranking_weekly
    ];
}

// --- HISTORICO DE PARTIDAS DO USUARIO ---
//Busca as últimas 20 partidas jogadas pelo usuário incluindo o nome da liga
$user_match_history = [];
$sql_user_match_history = "
    SELECT mh.score_gained, mh.played_at, l.name as league_name
    FROM $table_match_history mh
    LEFT JOIN $table_leagues l ON mh.league_id = l.id
    WHERE mh.user_id = '$user_id_escaped'
    ORDER BY mh.played_at DESC
    LIMIT 20";
$result_user_match_history = mysqli_query($conn, $sql_user_match_history);

if ($result_user_match_history) {
    while ($row = mysqli_fetch_assoc($result_user_match_history)) { // transforma as linhas em array
        $user_match_history[] = $row; // add linha na lista
    }
}

disconnect_db($conn);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="liga.css">
</head>
<body>
    <header>
        <nav>
            <a href="jogo.php">Jogar</a>
            <a href="liga.php">Ligas</a>
            <!-- so aparece perfil se o usuario estiver logado -->
            <a href="<?php echo $login ? 'perfil.php' : 'login.php'; ?>">
                <?php echo $login ? 'Perfil (' . htmlspecialchars($_SESSION["user_name"]) . ')' : 'Login'; ?>
            </a>
            <?php if ($login): ?>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        </nav>
    </header>

<main>
<h1>Gerenciamento de Ligas</h1>

<!-- Mostra as mensagens de sucesso, erro ou aviso geradas pelo PHP -->
<?php if (!empty($message)): ?>
    <div class="message">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<section id="create-league-section">
    <h2>Criar Nova Liga</h2>
    <form action="liga.php" method="post">
        <input type="hidden" name="action" value="create_league"> 
        <div>
            <label for="league_name">Nome da Liga:</label>
            <input type="text" id="league_name" name="league_name" required>
        </div>
        <div>
            <label for="league_keyword">Palavra-chave para Inscrição:</label>
            <input type="password" id="league_keyword" name="league_keyword" required>
        </div>
        <button type="submit">Criar Liga</button>
    </form>
</section>

<section id="join-league-section">
    <h2>Participar de Liga Existente</h2>
    <!-- Só exibe o formulário se o usuário não estiver em uma liga ativa -->
    <?php if ($user_current_league_id !== null): ?>
        <p>Você já está em uma liga ativa (<strong><?php echo htmlspecialchars($user_current_league_name); ?></strong>). Saia dela antes de entrar em outra.</p>
    <?php elseif (empty($all_leagues)): ?>
        <p>Nenhuma liga disponível para participar ainda.</p>
    <?php else: ?>
        <form action="liga.php" method="post">
            <input type="hidden" name="action" value="join_league">
            <div>
                <label for="league_id">Selecionar Liga:</label>
                <select id="league_id" name="league_id" required>
                    <option value="">-- Selecione uma Liga --</option>
                    <!-- exibe as ligas no dropdown -->
                    <?php foreach ($all_leagues as $league): ?>
                        <option value="<?php echo $league['id']; ?>"><?php echo htmlspecialchars($league['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="join_keyword">Palavra-chave:</label>
                <input type="password" id="join_keyword" name="join_keyword" required>
            </div>
            <button type="submit">Entrar na Liga</button>
        </form>
    <?php endif; ?>
</section>

<section id="my-leagues-section">
    <h2>Sua Liga Ativa</h2>
    <?php if ($user_current_league_id !== null): ?>
        <div class="league-card active-league-card">
            <h3>Você está ativo na liga: <strong><?php echo htmlspecialchars($user_current_league_name); ?></strong></h3>
            <p>Todas as suas partidas serão contabilizadas para esta liga.</p>
            <!-- Formulário para sair da liga -->
            <form action="liga.php" method="post">
                <input type="hidden" name="action" value="leave_league">
                <button type="submit" class="button-leave">Sair da Liga</button>
            </form>

            <?php
            // Mostra os rankings da liga ativa
            if (isset($league_rankings[$user_current_league_id])) { // se a variavel existir
                $active_league_data = $league_rankings[$user_current_league_id];
                ?>
                <h4>Pontuação Total na Liga</h4>
                <table class="league-ranking-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome do Jogador</th>
                            <th>Pontuação Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active_league_data['total'])): ?>
                            <tr><td colspan="3">Nenhum jogador pontuou nesta liga ainda.</td></tr>
                        <?php else: ?>
                            <!-- exibe os jogadores do ranking -->
                            <?php $pos_active = 1; foreach ($active_league_data['total'] as $player): ?>
                                <tr>
                                    <td><?php echo $pos_active++; ?></td>
                                    <td><?php echo htmlspecialchars($player['name']); ?></td>
                                    <td><?php echo htmlspecialchars($player['league_total_score']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h4>Pontuação Semanal na Liga</h4>
                <table class="league-ranking-table">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Nome do Jogador</th>
                            <th>Pontuação Semanal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active_league_data['weekly'])): ?>
                            <tr><td colspan="3">Nenhum jogador pontuou nesta liga esta semana.</td></tr>
                        <?php else: ?>
                            <!-- exibe os usuarios do ranking -->
                            <?php $pos_active_weekly = 1; foreach ($active_league_data['weekly'] as $player): ?>
                                <tr>
                                    <td><?php echo $pos_active_weekly++; ?></td>
                                    <td><?php echo htmlspecialchars($player['name']); ?></td>
                                    <td><?php echo htmlspecialchars($player['league_weekly_score']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    <?php else: ?>
        <p>Você não está em nenhuma liga ativa. Crie uma nova liga ou junte-se a uma existente para que suas pontuações sejam contabilizadas em uma liga! </p>
    <?php endif; ?>
</section>

<section id="global-ranking-section">
    <h2>Ranking Geral de Jogadores</h2>

    <h3>Pontuação Total (Desde o Início)</h3>
    <table id="global-ranking-total-table">
        <thead>
            <tr>
                <th>Posição</th>
                <th>Nome do Jogador</th>
                <th>Pontuação Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($global_ranking_total)): ?>
                <tr><td colspan="3">Nenhum jogador no ranking geral ainda.</td></tr>
            <?php else: ?>
                <!-- exibe os usuarios do ranking -->
                <?php $position = 1; foreach ($global_ranking_total as $player): ?>
                    <tr>
                        <td><?php echo $position++; ?></td>
                        <td><?php echo htmlspecialchars($player['name']); ?></td>
                        <td><?php echo htmlspecialchars($player['total_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Pontuação Semanal</h3>
    <table id="global-ranking-weekly-table">
        <thead>
            <tr>
                <th>Posição</th>
                <th>Nome do Jogador</th>
                <th>Pontuação Semanal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($global_ranking_weekly)): ?>
                <tr><td colspan="3">Nenhum jogador pontuou esta semana no ranking geral.</td></tr>
            <?php else: ?>
                <!-- Exibe os usuarios do ranking -->
                <?php $position = 1; foreach ($global_ranking_weekly as $player): ?>
                    <tr>
                        <td><?php echo $position++; ?></td>
                        <td><?php echo htmlspecialchars($player['name']); ?></td>
                        <td><?php echo htmlspecialchars($player['weekly_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section id="match-history-section">
    <h2>Meu Histórico de Partidas</h2>
    <?php if (empty($user_match_history)): ?>
        <p>Você ainda não jogou nenhuma partida registrada. Comece a jogar para registrar sua pontuação! </p>
    <?php else: ?>

        <!-- Tabela de partidas do usuario -->
        <table id="user-match-history-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Pontuação Ganha</th>
                    <th>Liga (se aplicável)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_match_history as $match): ?>
                    <tr>
                        <td><?php echo date('d/m/y H:i', strtotime($match['played_at'])); ?></td>
                        <td><?php echo htmlspecialchars($match['score_gained']); ?></td>
                        <td><?php echo $match['league_name'] ? htmlspecialchars($match['league_name']) : 'Geral'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
</main>
</body>
</html>