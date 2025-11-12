<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="criarConta.css" />
  </head>
  <body>
    <section>
      <!-- Formulario de Criar Conta -->
      <form action="" id="formulario">
        <!-- Titulo -->
        <h1 id="titulo-formulario">Criar Conta</h1>

        <!-- Input de nome -->
        <label for="name">Nome:</label>
        <input required type="text" name="name" id="name" placeholder="Seu nome" />

        <!-- Input de email -->
        <label for="email">Email:</label>
        <input required type="email" name="email" id="email" placeholder="Seu email" />

        <!-- Input de Criar senha -->
        <label for="senha">Criar senha:</label>
        <input
          required
          type="password"
          name="senha"
          id="senha"
          placeholder="Nova senha"
        />

        <!-- Botões -->
        <!-- <button type="submit" id="botao-voltar-login" >Voltar para Login</button> -->
        <a href="login.php" id="botao-voltar-login">Voltar para o Login</a>
        <button type="submit" id="botao-criarconta">Criar conta</button>
      </form>
    </section>
    <script src="jquery-3.2.0.min.js"></script>
    <script src="criarConta.js"></script>
  </body>
</html>
