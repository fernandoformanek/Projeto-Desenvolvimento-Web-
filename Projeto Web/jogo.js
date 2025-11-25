const frase = document.getElementById('frase');
const inputUsuario = document.getElementById('input-usuario');
const pontuacao = document.getElementById('pontos');
const botaoIniciar = document.getElementById('botao-iniciar');

// Vetor para frases usadas no jogo
let frases = [];

// Carrega as frases do arquivo .txt
fetch("frases.txt")
  .then(res => res.text())
  .then(texto => {            //Divide cada linha em um item do array
      frases = texto
        .split("\n")
        .map(f => f.trim())
        .filter(f => f.length > 0);

      console.log("Frases carregadas:", frases.length);

      // Agora sim, inicia o jogo
      IniciarJogo();
  })
  .catch(err => console.error("Erro ao carregar frases:", err));



let Pontos = 0;
let FraseAtual = '';
let tempototal = 30;
let tempoRestante = tempototal;
let timerId = null;
let fimtempo = 0;
let emJogo = false;

// Inicia o jogo ao clicar no botão
function IniciarJogo() {
  botaoIniciar.addEventListener('click', () => {
    EscolherFrase();
    Pontos = 0;
    pontuacao.textContent = Pontos;
    inputUsuario.value = '';
    inputUsuario.disabled = false;
    inputUsuario.focus();
    starttimer(30); // 30 segundos
  });
}

function starttimer(segundos) {
  if (timerId) clearInterval(timerId); // Limpa timer anterior se existir
  tempototal = segundos;
  fimtempo = Date.now() + segundos * 1000; // Multiplica por 1000 para converter em segundos
  tick();
  timerId = setInterval(tick, 100);
  emJogo = true;
}

function tick() {
  tempoRestante = fimtempo - Date.now();
  if (tempoRestante <= 0) {
    tempoRestante = 0;
    UpdateTimer(tempoRestante);
    endgame();
    return;
  }
  UpdateTimer(tempoRestante);
}

function UpdateTimer(segundos) {
  const segundosRestantes = Math.floor(segundos / 1000);
  document.getElementById('timer').textContent = segundosRestantes;
}

function endgame() {
  emJogo = false;
  clearInterval(timerId);
  inputUsuario.disabled = true;
  alert(`Tempo esgotado! Sua pontuação final é: ${Pontos}`);
  submitScore(Pontos);
}

function submitScore(score) {
  // Apenas submete se o score for maior que 0
  if (score > 0) {
    const scoreForm = document.getElementById('scoreForm');
    const finalScoreInput = document.getElementById('final_score_input');

    // Verifica se o formulário e o input oculto existem (só existem se o usuário estiver logado)
    if (scoreForm && finalScoreInput) {
      finalScoreInput.value = score; // Define o valor do input oculto
      scoreForm.submit(); // Submete o formulário, recarregando a página
    } else {
      console.warn(
        'Formulário de pontuação não encontrado ou usuário não logado. Pontuação não será salva automaticamente.'
      );
    }
  } else {
    console.log('Nenhum ponto marcado nesta partida, não salvando.');
  }
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

// Previne colar no campo de entrada
inputUsuario.addEventListener('paste', function (event) {
  event.preventDefault();

  alert('Não é permitido colar no jogo!');
});
