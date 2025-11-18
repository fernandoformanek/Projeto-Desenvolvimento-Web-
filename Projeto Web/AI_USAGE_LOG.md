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
