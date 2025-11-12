<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida JustType</title>
    <link rel="stylesheet" href="jogo.css">
</head>
<body>
    <header>
        <nav>
            <a href="login.php">Login</a>
            <a href="login.php">Ranking</a>
            <a href="login.php">Ligas</a>
            <a href="login.php">Perfil</a>
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
    <script src="jogo.js"></script>
    </section>
</body>
</html>