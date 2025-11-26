# Relatório de Uso de Inteligência Artificial Generativa

Este documento registra todas as interações significativas com ferramentas de IA generativa (como Gemini, ChatGPT, Copilot, etc.) durante o desenvolvimento deste projeto. O objetivo é promover o uso ético e transparente da IA como ferramenta de apoio, e não como substituta para a compreensão dos conceitos fundamentais.

## Política de Uso

O uso de IA foi permitido para as seguintes finalidades:

- Geração de ideias e brainstorming de algoritmos.
- Explicação de conceitos complexos.
- Geração de código boilerplate (ex: estrutura de classes, leitura de arquivos).
- Sugestões de refatoração e otimização de código.
- Debugging e identificação de causas de erros.
- Geração de casos de teste.

É proibido submeter código gerado por IA sem compreendê-lo completamente e sem adaptá-lo ao projeto. Todo trecho de código influenciado pela IA deve ser referenciado neste log.

---

## Registro de Interações

_Copie e preencha o template abaixo para cada interação relevante._

### Interação 1

- **Data:** 9/11/2025
- **Etapa do Projeto:** 1 - Começando o CSS
- **Ferramenta de IA Utilizada:** Chat GPT
- **Objetivo da Consulta:** O background estava com uma linha perceptível então precisei de ajuda para resolver esse problema.

- **Prompt(s) Utilizado(s):**
  1. "html, body { /_ Dimensões _/ height: 100%; /_ Cores _/ background-image: linear-gradient(#22152c, #422c52); } pq ele tem uma linha na parte de cima da tela?"
- **Resumo da Resposta da IA:**
  A IA deu várias soluções, mas observei que a que resolvia meu probkena era:
  /_ Cores _/
  background: linear-gradient(to bottom, #22152c 0%, #422c52 100%) no-repeat
  fixed;
- **Análise e Aplicação:**
  A resposta da IA foi útil para resolver o problema da linha no background alterando o gradiente.

- **Referência no Código:**
  A lógica inspirada por esta interação foi implementada no arquivo `criarConta.css` e `login.css`, especificamente na função `html,
body`, por volta da linha 17 nos dois arquivos.

---

### Interação 2

- **Data:** 13/11/2025
- **Etapa do Projeto:** 3 - Lógica do Jogo (Cronômetro)
- **Ferramenta de IA Utilizada:** Chat GPT
- **Objetivo da Consulta:** Apoio para a lógica das funções para criação do cronômetro
- **Prompt(s) Utilizado(s):** 1 - quero adicionar um timer no meu jogo de digitação, digita o maximo de frases ate acabar o tempo, me explique e ensine como fazer detalhadamente sem me dar o codigo pronto
  2 - me explique melhor o porque da TimerId
- **Resumo da Resposta da IA:** A IA me explicou a logica e o raciocinio que devo ter dividindo em sessões, 1 - Conceito geral, 2 - Variaveis, 3 - Funções, 4 - Como integrar no codigo, 5 - UX e comportamentos, 6 - Bugs a evitar, 7 - Testes, 8 - Sugestões
- **Análise e Aplicação:** Foi util para desenvolver o codigo e conseguir aplicar no nosso jogo, além de analisar o codigo de diferentes pontos de vista.
- **Referência no Código:** implementado no jogo.js, mais especificamente nas funções tick(), starttimer(), updatetimer(), endgame() e iniciarjogo().

---

### Interação 3

- **Data:** 18/11/2025
- **Etapa do Projeto:** Adicionar a lógica da pontuação no banco de dados
- **Ferramenta de IA Utilizada:** Chat GPT
- **Objetivo da Consulta:** Me instruir na implementação do sistema de pontos, como somar a pontuação, exibir no perfil e ranking. Para assim, ter uma base de código para essa funcionalidade
- **Prompt(s) Utilizado(s):** a cada partida o jogador ganha uma quantidade de pontos. Quero armazenar o total de pontos no banco de dados para exibir no perfil e no ranking das ligas. Me ajude a implementar isso
- **Resumo da Resposta da IA:** A IA me sugeriu uma maneira complicada de implementar esse sistema que não entendi bem. Portanto, pedi para a IA se basear na forma em que o sistema de login visto em aula se comunicava com o banco de dados. Dessa forma a IA me sugeriu usar um formulário oculto para armazenar a pontuação. Além disso, me guiou na alteração de funções do JS do jogo e a exibir os pontos no perfil e no ranking.
- **Análise e Aplicação:** Serviu para me dar uma boa base de código para o sistema de pontos com o banco de dados.
- **Referência no Código:** blocos php do jogo.php, função submitScore em jogo.js, alteração na função endgame no jogo.js, parte do php no perfil.php e dinamica no ranking.php

---

### Interação 4

- **Data:** 26/11/2025
- **Etapa do Projeto:** Fazer a parte das ligas
- **Ferramenta de IA Utilizada:** Chat GPT
- **Objetivo da Consulta:** Me dar um caminho e base para eu introduzir a logica de ligas no php e como fazer funcionar no jogo.php
- **Prompt(s) Utilizado(s):** Me ajude a fazer um sistema de Ligas para meu site:

O sistema deve disponibilizar a inscrição do usuário em ligas. Ligas são um conjunto de usuários que competem entre si. O usuário pode criar e se cadastrar em ligas. Para o cadastro do usuário em uma liga é necessário uma palavra-chave, definida pelo criador da liga.

A pontuação da liga deve ser exibida de duas formas:

pontuação desde a criação da liga; e
pontuação semanal.
Além da pontuação em suas respectivas ligas, o usuário também pode verificar sua pontuação geral, envolvendo todos os jogadores. Esse quadro também deve apresentar a pontuação desde a criação do sistema e pontuação semanal.

A qualquer momento, o usuário pode acessar um relatório com os dados de todas as partidas jogadas, com suas respectivas pontuações.

- **Resumo da Resposta da IA:** A IA me deu codigos completos e que não faziam parte do conteúdo estudado. Então pedi para ela me dar parte de codigos como base e me explicar para eu mesmo ir implementando. Antes disso, forneci a ela como é a lógica php que esta sendo utilizada na disciplina com mysqli, senhas criptografadas com hash md5, prevenção de sql injection com mysqli_real_escape_string, etc.
- **Análise e Aplicação:** A IA me indicou quais arquivos eu deveria alterar e como deveria ser feitas as alterações, me explicando a lógica por trás dos codigos.
  Depois disso, meu codigo apresentou diversos erros que a IA me ajudou a solucionar.
- **Referência no Código:** php da liga.php, principalmente na parte de entrar na liga, que tive bastante dificuldade

---
