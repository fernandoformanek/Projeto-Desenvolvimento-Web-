// Formulario
var formulario = document.getElementById('formulario');
var inputNome = document.getElementById('name');
var inputEmail = document.getElementById('email');
var inputSenha = document.getElementById('senha');

formulario.addEventListener('submit', function (ev) {
  ev.preventDefault();
  console.log(inputNome.value, inputEmail.value, inputSenha.value);

  alert(
    `Seja bem-vindo(a), ${inputNome.value}! Sua conta foi criada com sucesso`
  );
});

// Botões
var botoes = document.getElementsByTagName('button');
var botaoCriarConta = document.getElementById('botao-criarconta');
var botaoVoltarLogin = document.getElementById('botao-voltar-login');

// Animação do botão (provisório)
document.addEventListener('DOMContentLoaded', function () {
  var botoes = document.getElementsByTagName('button');
  for (let i = 0; i < botoes.length; i++) {
    // Use let para i no loop for
    // Adiciona o evento para quando o mouse ENTRA
    botoes[i].addEventListener('mouseover', function () {
      $(this).stop(true, true).animate(
        {
          padding: '1rem 2rem', // Aumenta o padding
          boxShadow: '0 5px 15px rgba(0,0,0,0.3)', // Adiciona sombra
        },
        200 // Duração da animação
      );
    });

    // Adiciona o evento para quando o mouse SAI
    botoes[i].addEventListener('mouseout', function () {
      $(this).stop(true, true).animate(
        {
          padding: '0.8rem 1.5rem', // Volta ao padding original
          boxShadow: '0 2px 5px rgba(0,0,0,0.1)', // Volta à sombra original
        },
        200 // Duração da animação
      );
    });
  }
});