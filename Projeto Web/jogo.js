const frase = document.getElementById('frase');
const inputUsuario = document.getElementById('input-usuario');
const pontuacao = document.getElementById('pontos');
const botaoIniciar = document.getElementById('botao-iniciar');

// Frases usadas no jogo
const frases = [
  'how are you',
  'i love apples',
  'i like this restaurant',
  'do you want to play?',
  'this is a fun game',
  'never give up',
];

let Pontos = 0;
let FraseAtual = '';

// Inicia o jogo ao clicar no botão
function IniciarJogo() {
  botaoIniciar.addEventListener('click', () => {
    EscolherFrase();
    Pontos = 0;
    pontuacao.textContent = Pontos;
    inputUsuario.value = '';
  });
}

// Escolhe uma frase aleatória e a exibe na tela
function EscolherFrase() {
  const indiceAleatorio = Math.floor(Math.random() * frases.length);
  FraseAtual = frases[indiceAleatorio];

  // Separar a frase em spans
  let html = '';
  for (let i = 0; i < FraseAtual.length; i++) {
    const letra = FraseAtual[i];
    html += `<span>${letra}</span>`;
  }

  // coloca na tela
  frase.innerHTML = html;
}

IniciarJogo();

inputUsuario.addEventListener('input', () => {
  const letras = frase.querySelectorAll('span');
  const valorDigitado = inputUsuario.value;

  // compara letra por letra e pinta
  for (let i = 0; i < letras.length; i++) {
    const letraCorreta = FraseAtual[i];
    const letraDigitada = valorDigitado[i];

    // Muda a cor conforme a digitação
    if (letraDigitada == null) {
      letras[i].style.color = 'white';
      letras[i].style.backgroundColor = 'transparent';
    } else if (letraDigitada === letraCorreta) {
      letras[i].style.color = 'green';
    } else {
      letras[i].style.color = 'red';
      // Muda o fundo do caractere espaço para vermelho se estiver errado
      if (letras[i].textContent === ' ') {
        letras[i].style.backgroundColor = 'rgba(255, 0, 0, 0.3)';
      } else {
        letras[i].style.backgroundColor = 'transparent';
      }
    }
  }

  // se digitou toda a frase corretamente
  if (valorDigitado === FraseAtual) {
    Pontos += 10;
    pontuacao.textContent = Pontos;
    inputUsuario.value = '';
    EscolherFrase(); // nova frase
  }
});
