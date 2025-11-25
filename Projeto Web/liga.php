<?php
require "authenticate.php"; // <--- Adicione esta linha
require "db_functions.php"; // <--- Adicione esta linha
require "db_credentials.php"; // <--- Adicione esta linha

// --- INÍCIO DA LÓGICA PARA BUSCAR RANKING ---
$players = []; // Array para armazenar os dados dos jogadores
$conn = connect_db();

// Consulta para buscar nome e pontuação total de todos os usuários,
// ordenados pela pontuação total (decrescente) e, em caso de empate, pelo nome (crescente)
$sql = "SELECT name, total_score FROM $table_users ORDER BY total_score DESC, name ASC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $players[] = $row;
    }
    mysqli_free_result($result); // Libera a memória do resultado
} else {
    // Em um ambiente de produção, este erro deveria ser logado, não exibido ao usuário.
    // Para depuração: error_log("Erro ao buscar ranking: " . mysqli_error($conn));
}

disconnect_db($conn);
// --- FIM DA LÓGICA PARA BUSCAR RANKING ---
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

    <h1 id="titulo">Ranking de jogadores da Liga</h1>
    <section id="secao-ranking">
        <table id="tabela-ranking">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Nome do Jogador</th>
                    <th>Pontuação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($players)): ?>
                    <tr>
                        <td colspan="3">Nenhum jogador no ranking ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php $position = 1; ?>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?php echo $position++; ?></td>
                            <td><?php echo htmlspecialchars($player['name']); ?></td>
                            <td><?php echo htmlspecialchars($player['total_score']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>       
            </tbody>
        </table>
    </section>
</body>
</html>