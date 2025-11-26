<?php
require "db_functions.php";

$error = false;
$success = false;
$name = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {

    $conn = connect_db();

    $name = mysqli_real_escape_string($conn,$_POST["name"]);
    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $password = mysqli_real_escape_string($conn,$_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn,$_POST["confirm_password"]);

    if ($password == $confirm_password) {
      $password = md5($password);

      $sql = "INSERT INTO $table_users
              (name, email, password) VALUES
              ('$name', '$email', '$password');";

      if(mysqli_query($conn, $sql)){
        $success = true;
      }
      else {
        $error_msg = mysqli_error($conn);
        $error = true;
      }
    }
    else {
      $error_msg = "Senha não confere com a confirmação.";
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
    <link rel="stylesheet" href="criarConta.css" />
  </head>
  <body>

  <?php if ($success): 
      header("Refresh: 0; url=login.php");

    
  ?>
      
<?php endif; ?>

<?php if ($error): ?>
  <h3 style="color:red;"><?php echo $error_msg; ?></h3>
<?php endif; ?>


    <section>
      <!-- Formulario de Criar Conta -->
      <form action="CriarConta.php" method="post" id="formulario">
        <!-- Titulo -->
        <h1 id="titulo-formulario">Criar Conta</h1>

        <!-- Input de nome -->
        <label for="name">Nome:</label>
        <input required type="text" name="name" id="name" value="<?php echo $name; ?>" placeholder="Seu nome" />

        <!-- Input de email -->
        <label for="email">Email:</label>
        <input required type="email" name="email" value="<?php echo $email; ?>" id="email" placeholder="Seu email" />

        <!-- Input de Criar senha -->
        <label for="password">Criar senha:</label>
        <input
          required
          type="password"
          name="password"
          id="senha"
          value = ""
          placeholder="Nova senha"
        />

          <label for="confirm_password">Confirmação da Senha: </label>
          <input type="password" name="confirm_password" value="" required placeholder="Nova senha">

        <!-- Botões -->
        <!-- <button type="submit" id="botao-voltar-login" >Voltar para Login</button> -->
        <a href="login.php" id="botao-voltar-login">Voltar para o Login</a>
        <button type="submit" name="submit" id="botao-criarconta">Criar conta</button>
      </form>
    </section>
    <script src="jquery-3.2.0.min.js"></script>
    <script src="criarConta.js"></script>
  </body>
</html>
