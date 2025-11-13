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

*Copie e preencha o template abaixo para cada interação relevante.*

### Interação 1

- **Data:** 9/11/2025
- **Etapa do Projeto:** 1 - Começando o CSS
- **Ferramenta de IA Utilizada:** Chat GPT
- **Objetivo da Consulta:** O background estava com uma linha perceptível então precisei de ajuda para resolver esse problema.

- **Prompt(s) Utilizado(s):**
  1. "html, body { /* Dimensões */ height: 100%; /* Cores */ background-image: linear-gradient(#22152c, #422c52); } pq ele tem uma linha na parte de cima da tela?"
- **Resumo da Resposta da IA:**
  A IA deu várias soluções, mas observei que a que resolvia meu probkena era:
  /* Cores */
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
- **Prompt(s) Utilizado(s):**  1 - quero adicionar um timer no meu jogo de digitação, digita o maximo de frases ate acabar o tempo, me explique e ensine como fazer detalhadamente sem me dar o codigo pronto
2 - me explique melhor o porque da TimerId
- **Resumo da Resposta da IA:** A IA me explicou a logica e o raciocinio que devo ter dividindo em sessões, 1 - Conceito geral, 2 - Variaveis, 3 - Funções, 4 - Como integrar no codigo, 5 - UX e comportamentos, 6 - Bugs a evitar, 7 - Testes, 8 - Sugestões 
- **Análise e Aplicação:** Foi util para desenvolver o codigo e conseguir aplicar no nosso jogo, além de analisar o codigo de diferentes pontos de vista.
- **Referência no Código:** implementado no jogo.js, mais especificamente nas funções tick(), starttimer(), updatetimer(), endgame() e iniciarjogo().

---
