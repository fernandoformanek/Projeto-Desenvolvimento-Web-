<?php
require "authenticate.php";
require "db_functions.php";

$error = false;
$password = $email = "";

if ($login) {
    header("Location: jogo.php"); // Redireciona para a página do jogo
    exit();
}


if (!$login && $_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["email"]) && isset($_POST["password"])) {

    $conn = connect_db();

    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $password = mysqli_real_escape_string($conn,$_POST["password"]);
    $password = md5($password);

    $sql = "SELECT u.id, u.name, u.email, u.password, u.current_league_id, l.name as current_league_name
        FROM $table_users u
        LEFT JOIN $table_leagues l ON u.current_league_id = l.id
        WHERE u.email = '$email';";

    $result = mysqli_query($conn, $sql);
    if($result){
      if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user["password"] == $password) {

          $_SESSION["user_id"] = $user["id"];
          $_SESSION["user_name"] = $user["name"];
          $_SESSION["user_email"] = $user["email"];
          $_SESSION["user_current_league_id"] = $user["current_league_id"];
          $_SESSION["user_current_league_name"] = $user["current_league_name"];

          header("Location: jogo.php");
          exit();
        }
        else {
          $error_msg = "Senha incorreta!";
          $error = true;
        }
      }
      else{
        $error_msg = "Usuário não encontrado!";
        $error = true;
      }
    }
    else {
      $error_msg = mysqli_error($conn);
      $error = true;
    }
  }
  else {
    $error_msg = "Por favor, preencha todos os dados.";
    $error = true;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="login.css" />
  </head>
  <body>

    <?php if ($error): ?>
      <h3 style="color:red;"><?php echo $error_msg; ?></h3>
    <?php endif; ?>


    <section>
      <!-- Formulario de Login -->
      <form action="login.php" id="formulario" method="post">
        <!-- Titulo -->
        <h1 id="titulo-formulario">Login</h1>

        <!-- Input de email -->
        <label for="email">Email:</label>
        <input required type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Seu email" />

        <!-- Input de senha -->
        <label for="password">Senha:</label>
        <input required type="password" name="password" value="" id="senha" placeholder="Senha" />

        <!-- Botões -->
        <!-- <button type="submit" id="botao-criarconta">Criar nova conta</button> -->
        <a href="criarConta.php" id="botao-cadastre-se">Criar Conta</a>
        <button type="submit" id="botao-logar">Logar</button>
      </form>
    </section>
  </body>
</html>
