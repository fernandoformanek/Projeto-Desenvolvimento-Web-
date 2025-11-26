<?php
require "authenticate.php"; // <--- Adicione esta linha
require "db_functions.php"; // <--- Adicione esta linha
require "db_credentials.php"; // <--- Adicione esta linha

// Garante que o usuário esteja logado para acessar esta página
if (!$login) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$conn = connect_db(); // Abre a conexão com o banco de dados 

$message = '';

// --- CRIAR NOVA LIGA ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create_league") {
    $league_name = trim($_POST["league_name"]);
    $league_keyword = trim($_POST["league_keyword"]);

    $league_name_escaped = mysqli_real_escape_string($conn, $league_name);
    $hashed_keyword = md5($league_keyword); // A palavra chave sera armazenada como hash MD5
    $hashed_keyword_escaped = mysqli_real_escape_string($conn, $hashed_keyword);
    $user_id_escaped = mysqli_real_escape_string($conn, $user_id);

    // verificaçoes para criação da liga
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

                // O criador da liga entra automaticamente nela
                $sql_join_creator = "INSERT INTO $table_user_leagues (user_id, league_id) VALUES ('$user_id_escaped', '$new_league_id_escaped')";
                mysqli_query($conn, $sql_join_creator);

                $message = "<p style='color: green;'>Liga '<strong>" . htmlspecialchars($league_name) . "</strong>' criada com sucesso e você já faz parte dela! </p>";
            } else {
                $message = "<p style='color: red;'>Erro ao criar liga: " . mysqli_error($conn) . " </p>";
            }
        }
    }
}

// --- ENTRAR EM LIGA EXISTENTE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "join_league") {
    $league_id_to_join = (int)$_POST["league_id"];
    $entered_keyword = trim($_POST["join_keyword"]);

    // ESCAPAR TODOS OS INPUTS DO USUÁRIO
    $league_id_to_join_escaped = mysqli_real_escape_string($conn, $league_id_to_join);
    $entered_keyword_escaped = mysqli_real_escape_string($conn, $entered_keyword); // Palavra chave bruta
    $user_id_escaped = mysqli_real_escape_string($conn, $user_id);

      if (empty($entered_keyword) || $league_id_to_join <= 0) {
        $message = "<p style='color: red;'>Selecione uma liga e forneça a palavra-chave. </p>";
    } else {
        // Verifica se o usuário já está nesta liga
        $sql_check_user_league = "SELECT COUNT(*) FROM $table_user_leagues WHERE user_id = '$user_id_escaped' AND league_id = '$league_id_to_join_escaped'";
        $result_check_user_league = mysqli_query($conn, $sql_check_user_league);
        $row_check_user_league = mysqli_fetch_row($result_check_user_league);
        $count = $row_check_user_league[0];

        if ($count > 0) {
            $message = "<p style='color: orange;'>Você já faz parte desta liga. </p>";
        } else {
            // Busca a palavra-chave da liga (hash MD5)
            $sql_get_league_keyword = "SELECT keyword, name FROM $table_leagues WHERE id = '$league_id_to_join_escaped'";
            $result_get_league_keyword = mysqli_query($conn, $sql_get_league_keyword);
            $league_data = mysqli_fetch_assoc($result_get_league_keyword);

             if ($league_data && md5($entered_keyword_escaped) === $league_data['keyword']) {
                // Junta o usuário à liga
                $sql_join_league = "INSERT INTO $table_user_leagues (user_id, league_id) VALUES ('$user_id_escaped', '$league_id_to_join_escaped')";
                if (mysqli_query($conn, $sql_join_league)) {
                    $message = "<p style='color: green;'>Você entrou na liga '<strong>" . htmlspecialchars($league_data['name']) . "</strong>' com sucesso! </p>";
                } else {
                    $message = "<p style='color: red;'>Erro ao entrar na liga: " . mysqli_error($conn) . " </p>";
                }
            } else {
                $message = "<p style='color: red;'>Palavra-chave incorreta para esta liga. </p>";
            }
        }
    }
}

// --- EXIBIR DADOS ---

// Todas as ligas disponiveis para o usuário entrar
$all_leagues = [];
$sql_all_leagues = "SELECT id, name FROM $table_leagues ORDER BY name ASC";
$result_all_leagues = mysqli_query($conn, $sql_all_leagues);
if ($result_all_leagues) {
    while ($row = mysqli_fetch_assoc($result_all_leagues)) {
        $all_leagues[] = $row;
    }
}

// Ligas que o usuário logado pertence
$user_leagues_data = [];
$user_id_escaped = mysqli_real_escape_string($conn, $user_id); // Garante que o user_id está escapado aqui também

$sql_user_leagues = "SELECT l.id, l.name 
FROM $table_leagues l 
JOIN $table_user_leagues ul ON l.id = ul.league_id 
WHERE ul.user_id = '$user_id_escaped' 
ORDER BY l.name ASC";


$result_user_leagues = mysqli_query($conn, $sql_user_leagues);
if ($result_user_leagues) {
    while ($row = mysqli_fetch_assoc($result_user_leagues)) {
        $user_leagues_data[] = $row;
    }
}

// --- RANKING GLOBAL ---
$global_ranking_total = [];
$sql_global_total = "SELECT name, total_score FROM $table_users ORDER BY total_score DESC, name ASC";
$result_global_total = mysqli_query($conn, $sql_global_total);
if ($result_global_total) {
    while ($row = mysqli_fetch_assoc($result_global_total)) {
        $global_ranking_total[] = $row;
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
    while ($row = mysqli_fetch_assoc($result_global_weekly)) {
        $global_ranking_weekly[] = $row;
    }
}

// --- RANKINGS POR LIGA ---
$league_rankings = [];
foreach ($user_leagues_data as $league) {
    $league_id = $league['id'];
    $league_name = $league['name'];
    $league_id_escaped = mysqli_real_escape_string($conn, $league_id);

    // Ranking Total da Liga
    $league_ranking_total = [];
    $sql_league_total = "
        SELECT u.name, SUM(mh.score_gained) as league_total_score
        FROM $table_users u
        JOIN $table_match_history mh ON u.id = mh.user_id
        WHERE mh.league_id = '$league_id_escaped'
        AND u.id IN (SELECT user_id FROM $table_user_leagues WHERE league_id = '$league_id_escaped')
        GROUP BY u.id, u.name
        ORDER BY league_total_score DESC, u.name ASC";
    $result_league_total = mysqli_query($conn, $sql_league_total);
    if ($result_league_total) {
        while ($row = mysqli_fetch_assoc($result_league_total)) {
            $league_ranking_total[] = $row;
        }
    }

    // Ranking Semanal da Liga
    $league_ranking_weekly = [];
    $sql_league_weekly = "
        SELECT u.name, SUM(mh.score_gained) as league_weekly_score
        FROM $table_users u
        JOIN $table_match_history mh ON u.id = mh.user_id
        WHERE mh.league_id = '$league_id_escaped' AND mh.played_at >= '$start_of_week_escaped'
        AND u.id IN (SELECT user_id FROM $table_user_leagues WHERE league_id = '$league_id_escaped')
        GROUP BY u.id, u.name
        ORDER BY league_weekly_score DESC, u.name ASC";
    $result_league_weekly = mysqli_query($conn, $sql_league_weekly);
    if ($result_league_weekly) {
        while ($row = mysqli_fetch_assoc($result_league_weekly)) {
            $league_ranking_weekly[] = $row;
        }
    }

    $league_rankings[$league_id] = [
        'name' => $league_name,
        'total' => $league_ranking_total,
        'weekly' => $league_ranking_weekly
    ];
}

// --- HISTORICO DE PARTIDAS DO USUARIO ---
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
    while ($row = mysqli_fetch_assoc($result_user_match_history)) {
        $user_match_history[] = $row;
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
    <?php if (empty($all_leagues)): ?>
        <p>Nenhuma liga disponível para participar ainda.</p>
    <?php else: ?>
        <form action="liga.php" method="post">
            <input type="hidden" name="action" value="join_league">
            <div>
                <label for="league_id">Selecionar Liga:</label>
                <select id="league_id" name="league_id" required>
                    <option value="">-- Selecione uma Liga --</option>
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
    <h2>Minhas Ligas</h2>
    <?php if (empty($user_leagues_data)): ?>
        <p>Você não está participando de nenhuma liga ainda. Crie uma ou junte-se a uma existente! </p>
    <?php else: ?>
        <ul class="league-list">
            <?php foreach ($user_leagues_data as $league): ?>
                <li class="league-item">
                    <div class="league-card">
                        <h3><?php echo htmlspecialchars($league['name']); ?></h3>
                        <p>Aqui estão os rankings desta liga.</p>

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
                                <?php if (empty($league_rankings[$league['id']]['total'])): ?>
                                    <tr><td colspan="3">Nenhum jogador pontuou nesta liga ainda.</td></tr>
                                <?php else: ?>
                                    <?php $pos = 1; foreach ($league_rankings[$league['id']]['total'] as $player): ?>
                                        <tr>
                                            <td><?php echo $pos++; ?></td>
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
                                <?php if (empty($league_rankings[$league['id']]['weekly'])): ?>
                                    <tr><td colspan="3">Nenhum jogador pontuou nesta liga esta semana.</td></tr>
                                <?php else: ?>
                                    <?php $pos = 1; foreach ($league_rankings[$league['id']]['weekly'] as $player): ?>
                                        <tr>
                                            <td><?php echo $pos++; ?></td>
                                            <td><?php echo htmlspecialchars($player['name']); ?></td>
                                            <td><?php echo htmlspecialchars($player['league_weekly_score']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
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
                <?php $position = 1; foreach ($global_ranking_weekly as $player): ?>
                    <tr>
                        <td><?php echo $pos++; ?></td>
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
                        <td><?php echo date('d/m/Y H:i:s', strtotime($match['played_at'])); ?></td>
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