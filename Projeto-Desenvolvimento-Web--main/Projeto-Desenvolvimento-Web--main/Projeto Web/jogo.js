const frase = document.getElementById("frase");
const inputUsuario = document.getElementById("input-usuario");
const pontuacao = document.getElementById("pontuacao");
const botaoIniciar = document.getElementById("botao-iniciar");

const frases = [ "how are you", "i love apples", "i like this restaurant", "do you want to play?", "this is a fun game", "never give up" ];

let Pontos = 0;
let FraseAtual = "";

function IniciarJogo() {
    botaoIniciar.addEventListener("click", () => {
        EscolherFrase();
    Pontos = 0;
    pontuacao.textContent = Pontos;
    inputUsuario.value = "";
    })
};
function EscolherFrase() {
    const ÍndiceAleatório = Math.floor(Math.random() * frases.length);
    FraseAtual = frases[ÍndiceAleatório];
    frase.textContent = FraseAtual;
}

IniciarJogo();

inputUsuario.addEventListener("input", () =>{
   if (inputUsuario.value === FraseAtual) { 
    Pontos = Pontos + 10;
    pontuacao.textContent = Pontos;
    inputUsuario.value = "";
    EscolherFrase();
   }
});


