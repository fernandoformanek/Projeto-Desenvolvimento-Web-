## Funcionalidades Principais

- **Registro de Usuários:** Permite que novos usuários criem uma conta no sistema.
- **Autenticação de Usuários:** Login de usuários existentes com gerenciamento de sessões.
- **Jogo de Digitação JustType:** Um jogo em que os usuários digitam frases para acumular pontos dentro de um tempo limite.
- **Gerenciamento de Pontuações:** Salva as pontuações dos jogadores após cada partida, atualizando seus totais.
- **Criação de Ligas:** Usuários logados podem criar suas próprias ligas com palavras-chave de acesso.
- **Participação em Ligas:** Usuários podem buscar e entrar em ligas existentes usando uma palavra-chave.
- **Rankings Detalhados:**
  - Rankings Globais (total e semanal) para todos os jogadores do sistema.
  - Rankings de Liga (total e semanal) para jogadores dentro de uma liga específica.
- **Saída de Ligas:** Permite que os usuários saiam de uma liga ativa.
- **Reset de Pontuação Semanal:** Lógica para gerenciar e resetar pontuações semanais.

## Tecnologias Utilizadas

- **Backend:**
  - PHP (para lógica de servidor, banco de dados e gerenciamento de sessões)
  - MySQL (Sistema de Gerenciamento de Banco de Dados)
- **Frontend:**
  - HTML5 (Estrutura da página)
  - CSS3 (Estilização)
  - JavaScript (Lógica do jogo, interatividade e comunicação assíncrona para envio de pontuações)

## Sistema de Autenticação

O sistema de autenticação é essencial para a interação do usuário com o jogo e as ligas. Ele é composto por dois arquivos principais: `criarConta.php` e `login.php`.

### `criarConta.php`

Este codigo permite que novos usuários se registrem no sistema.

- **Processo:**
  1.  O usuário acessa a página `criarConta.php` e preenche um formulário com Nome, E-mail, Senha e Confirmação de Senha.
  2.  No lado do servidor (PHP), é verificado se as senhas coincidem.
  3.  A senha fornecida é convertida para `MD5`.
  4.  Um novo registro é inserido na tabela `users` com o nome, e-mail e a senha hashed.
- **Redirecionamento:** Em caso de sucesso na criação da conta, o usuário é automaticamente redirecionado para a página de login (`login.php`).
- **Tratamento de Erros:** Mensagens de erro são exibidas diretamente na página em caso de senhas não coincidentes, campos não preenchidos ou falha na inserção no banco de dados (ex: e-mail já cadastrado).

### `login.php`

Este código gerencia a autenticação de usuários existentes e o estabelecimento de sessões.

- **Processo:**
  1.  Ao acessar `login.php`, o sistema verifica se o usuário já está logado (sessão ativa). Se sim, ele é automaticamente redirecionado para a página do jogo (`jogo.php`).
  2.  Caso contrário, o usuário insere seu E-mail e Senha em um formulário.
  3.  A senha fornecida é convertida para `MD5` para comparação com a senha hashed armazenada no banco de dados.
  4.  O sistema consulta a tabela `users` (e `leagues` para obter o nome da liga, se o usuário estiver em uma) para encontrar um usuário com o e-mail e a senha hashed correspondentes.
  5.  Em caso de autenticação bem-sucedida, as informações essenciais do usuário (`user_id`, `user_name`, `user_email`, `user_current_league_id`, `user_current_league_name`) são armazenadas na variável de sessão global (`$_SESSION`), e o usuário é redirecionado para a página do jogo (`jogo.php`).
- **Tratamento de Erros:** Mensagens de erro são exibidas na página para credenciais inválidas (senha incorreta, usuário não encontrado) ou campos não preenchidos.

## Como o Jogo JustType Funciona

O JustType é uma jogo de digitação para treinar os usuários a digitar em inglês.

### Objetivo

O objetivo é digitar o maior número de frases corretas possível dentro de um tempo limite, acumulando pontos.

### Fluxo de Jogo

1.  **Início:** Ao acessar `jogo.php`, o usuário clica no botão "Iniciar Jogo".
2.  **Carregamento de Frases:** Frases pré-definidas são carregadas de um arquivo `frases.txt` e uma é exibida na tela.
3.  **Contagem Regressiva:** Um timer de 30 segundos é iniciado.
4.  **Digitação:**
    - O usuário digita a frase exibida no campo de entrada.
    - **Feedback Visual:** Conforme o usuário digita, o sistema compara cada caractere digitado com a frase correta:
      - Caracteres corretos são exibidos em **verde**.
      - Caracteres incorretos são exibidos em **vermelho**.
      - (Não é permitido colar texto no campo de entrada.)
    - **Pontuação por Frase:** Assim que uma frase é digitada **total e corretamente**, o usuário ganha 10 pontos, sua pontuação é atualizada na tela, o campo de entrada é limpo e uma nova frase é apresentada.
5.  **Fim do Jogo:** O jogo termina quando o timer chega a zero.
6.  **Pontuação Final:** Uma caixa de alerta exibe a pontuação total do jogador na partida.
7.  **Registro da Pontuação:**
    - Se o usuário estiver logado e tiver feito mais de 0 pontos, a pontuação final é enviada automaticamente para o servidor (via um formulário oculto) para ser salva no banco de dados.
    - A página `jogo.php` processa esta submissão, atualizando o `total_score` do usuário e registrando a partida na tabela `match_history`.

## Como o Sistema de Ligas Funciona

O sistema de ligas permite que os usuários compitam em grupos.

### Conceito

Usuários podem criar suas próprias ligas com palavra chave para acesso e convidar outros jogadores. As ligas possuem rankings independentes que refletem a performance dos membros.

### Criação de Liga

1.  Um usuário logado pode navegar até a página `liga.php` e optar por criar uma nova liga.
2.  Ele fornece um nome para a liga e uma palavra-chave de acesso.
3.  A liga é registrada na tabela `leagues`, com o usuário criador sendo definido como `creator_id`. A palavra-chave é armazenada de forma hashed (MD5).
4.  Após a criação, o usuário é automaticamente associado a essa nova liga.

### Participação em Liga

1.  Na página `liga.php`, usuários logados podem ver uma lista das ligas existentes.
2.  Para entrar em uma liga, o usuário seleciona a liga desejada e informa a palavra-chave.
3.  O sistema verifica se a palavra-chave fornecida corresponde à palavra-chave registrada para a liga.
4.  Em caso de sucesso, o campo `current_league_id` do usuário na tabela `users` é atualizado com o ID da liga, e as informações da liga são armazenadas na sessão do usuário.

### Gerenciamento de Pontuações em Liga

- Quando um usuário em uma liga joga uma partida e sua pontuação é registrada, o sistema não apenas atualiza sua pontuação global (`total_score`), mas também mantém as pontuações específicas da liga (`league_total_score` e `league_weekly_score`) através da soma `match_history` e/ou colunas específicas na tabela `users`.
- O sistema inclui uma lógica para resetar as pontuações semanais (`weekly_score` e `league_weekly_score`) no início de cada nova semana, garantindo que os rankings semanais sejam atualizados corretamente.

### Rankings

A página `liga.php` exibe diferentes tipos de rankings:

- **Rankings Globais:**
  - **Total:** Mostra a pontuação acumulada por todos os usuários do sistema em todas as suas partidas.
  - **Semanal:** Mostra a pontuação acumulada por todos os usuários na semana atual.
- **Rankings de Liga:**
  - **Total:** Mostra a pontuação acumulada pelos membros da liga ativa do usuário.
  - **Semanal:** Mostra a pontuação acumulada pelos membros da liga ativa na semana atual.
- **Ordenação:** Todos os rankings são ordenados em ordem decrescente pela pontuação (`DESC`) e, em caso de empate, pelo nome do jogador em ordem crescente (`ASC`).

### Sair da Liga

Usuários podem optar por sair da liga ativa. Ao fazer isso, seu `current_league_id` na tabela `users` é definido como `NULL`, desassociando-o da liga.

## Estrutura do Banco de Dados

O sistema utiliza um banco de dados MySQL com as seguintes tabelas principais:

### `users`

Armazena informações dos usuários, suas pontuações e a qual liga pertencem.

<table class="data-table">
  <thead>
    <tr>
      <th scope="col">Coluna</th>
      <th scope="col">Tipo</th>
      <th scope="col">Descrição</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>`id`</td>
      <td>`INT`</td>
      <td>Chave primária, auto-incremento.</td>
    </tr>
    <tr>
      <td>`name`</td>
      <td>`VARCHAR(255)`</td>
      <td>Nome de usuário.</td>
    </tr>
    <tr>
      <td>`email`</td>
      <td>`VARCHAR(255)`</td>
      <td>Endereço de e-mail do usuário (unico).</td>
    </tr>
    <tr>
      <td>`password`</td>
      <td>`VARCHAR(255)`</td>
      <td>Senha do usuário (hashed md5).</td>
    </tr>
    <tr>
      <td>`total_score`</td>
      <td>`INT`</td>
      <td>Pontuação total acumulada em todas as partidas.</td>
    </tr>
    <tr>
      <td>`current_league_id`</td>
      <td>`INT`</td>
      <td>ID da liga ativa do usuário (`NULL` se não estiver em nenhuma liga).</td>
    </tr>
  </tbody>
</table>

### `leagues`

Armazena informações sobre as ligas criadas.

<table class="data-table">
  <thead>
    <tr>
      <th scope="col">Coluna</th>
      <th scope="col">Tipo</th>
      <th scope="col">Descrição</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>`id`</td>
      <td>`INT`</td>
      <td>Chave primária, auto-incremento.</td>
    </tr>
    <tr>
      <td>`name`</td>
      <td>`VARCHAR(255)`</td>
      <td>Nome da liga.</td>
    </tr>
    <tr>
      <td>`keyword`</td>
      <td>`VARCHAR(255)`</td>
      <td>Palavra-chave para entrar na liga (hashed md5).</td>
    </tr>
    <tr>
      <td>`creator_id`</td>
      <td>`INT`</td>
      <td>ID do usuário que criou a liga.</td>
    </tr>
    <tr>
      <td>`created_at`</td>
      <td>`DATETIME`</td>
      <td>Timestamp da criação da liga.</td>
    </tr>
  </tbody>
</table>

### `match_history`

Registra cada partida jogada.

<table class="data-table">
  <thead>
    <tr>
      <th scope="col">Coluna</th>
      <th scope="col">Tipo</th>
      <th scope="col">Descrição</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>`id`</td>
      <td>`INT`</td>
      <td>Chave primária, auto-incremento.</td>
    </tr>
    <tr>
      <td>`user_id`</td>
      <td>`INT`</td>
      <td>ID do usuário que jogou a partida.</td>
    </tr>
    <tr>
      <td>`league_id`</td>
      <td>`INT`</td>
      <td>ID da liga em que o usuário estava na hora da partida (`NULL` se não estava em liga).</td>
    </tr>
    <tr>
      <td>`score_gained`</td>
      <td>`INT`</td>
      <td>Pontuação obtida na partida.</td>
    </tr>
    <tr>
      <td>`played_at`</td>
      <td>`DATETIME`</td>
      <td>Data e hora em que a partida foi registrada.</td>
    </tr>
  </tbody>
</table>

## Fluxo Básico de Utilização

1.  **Acessar o Sistema:** O usuário pode começar fazendo login ou registrando uma nova conta.
2.  **Jogar:** Acesse a página do jogo (`jogo.php`) para iniciar uma partida de digitação. Ao final, se logado, sua pontuação será salva.
3.  **Gerenciar Ligas:** Na página de ligas (`liga.php`), o usuário pode:
    - Ver os rankings globais e da sua liga (se estiver em uma).
    - Criar uma nova liga.
    - Entrar em uma liga existente (precisando da palavra-chave).
    - Sair da liga atual.
4.  **Visualizar Perfil:** A página de perfil (`perfil.php`) (se implementada) pode exibir informações do usuário e suas estatísticas.
5.  **Sair:** Fazer logout do sistema.
