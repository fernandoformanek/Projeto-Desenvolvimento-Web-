<?php
require "db_functions.php";
require "db_credentials.php";
require "authenticate.php"; // Verifica se o usuário está logado e define $login, $user_id, etc.

// Se o usuário não estiver logado, redireciona para a página de login
if (!$login) {
    header("Location: login.php");
    exit();
}

// informações do usuário da sessão
$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$user_email = $_SESSION["user_email"];
$total_score = 0; 

$conn = connect_db();

$user_id_escaped = mysqli_real_escape_string($conn, $user_id);

// Busca a pontuação total do usuário no banco de dados
$sql = "SELECT total_score FROM $table_users WHERE id = '$user_id_escaped'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $total_score = $user_data['total_score'];
} 

disconnect_db($conn);

$has_created_league = false;
$conn_ach = connect_db(); // Use a função de conexão
$user_id_escaped = mysqli_real_escape_string($conn_ach, $user_id); // Escapar o user_id

$sql_check_league_creation = "SELECT COUNT(*) FROM $table_leagues WHERE creator_id = '$user_id_escaped'";
$result_check_league_creation = mysqli_query($conn_ach, $sql_check_league_creation);

if ($result_check_league_creation) { // Verifica se a consulta foi bem-sucedida
    $row_check_league_creation = mysqli_fetch_row($result_check_league_creation);
    $league_count = $row_check_league_creation[0];

    if ($league_count > 0) {
        $has_created_league = true;
    }
}
disconnect_db($conn_ach); 

$conquistas = [
    [
        "title" => "Primeiros Passos",
        "desc" => "Ganhe 100 pontos totais.",
        "required" => 100,
        "icon" => "🏁"
    ],
    [
        "title" => "Aquecendo os Dedos",
        "desc" => "Ganhe 500 pontos totais.",
        "required" => 500,
        "icon" => "🔥"
    ],
    [
        "title" => "Viciado",
        "desc" => "Ganhe 1000 pontos totais.",
        "required" => 1000,
        "icon" => "⌨️"
    ],
    [
        "title" => "Dedos de Ouro",
        "desc" => "Ganhe 2000 pontos totais.",
        "required" => 2000,
        "icon" => "🟡"
    ],
    [
        "title" => "O Mestre",
        "desc" => "Ganhe 3000 pontos totais.",
        "required" => 3000,
        "icon" => "👑"
    ],
    [
        "title" => "Embaixador",
        "desc" => "Crie uma liga.",
        "required" => "league",
        "icon" => "🌍"
    ]
];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($user_name); ?></title>
    <link rel="stylesheet" href="perfil.css"> <!-- Reutilizando seu CSS existente -->
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

    <section id="secao-perfil" >
        <div id="card-perfil">
            <div class="avatar-perfil">
                <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Avatar do Usuário">
            </div>
            <h1>Bem-vindo, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <h2>Seu Perfil</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <p><strong>Pontuação Total:</strong> <span class="score"><?php echo $total_score; ?></span></p>
        </div>
    </section>
    <section class="conquistas">
<h2>Conquistas</h2>

<div class="conquistas-container">

<?php foreach ($conquistas as $a): ?>

    <?php
        // Lógica individual de desbloqueio
        if ($a["required"] === "league") {
             $desbloqueada = $has_created_league; 
        } else {
            $desbloqueada = ($total_score >= $a["required"]);
        }
    ?>

 <!-- AS OUTRAS CONQUISTAS ESTAO NO ARRAY $CONQUISTAS, NO TOPO DO ARQUIVO -->   

    <div class="<?php echo $desbloqueada ? 'conquista-desbloqueada' : 'conquista-bloqueada'; ?>">
        <div class="icon"><?php echo $a["icon"]; ?></div>
        <h3><?php echo $a["title"]; ?></h3>
        <p><?php echo $a["desc"]; ?></p>
    </div>

<?php endforeach; ?>

</div>
</section>

</body>
</html>