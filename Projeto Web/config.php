<?php
    $dbhost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'formulario-web';

    $conexao = new mysqli($dbhost,$dbUsername,$dbPassword,$dbName);

    // ADICIONE ESTE BLOCO PARA FORÇAR ERRO:
    if($conexao->connect_errno)
    {
       die("ERRO FATAL NA CONEXÃO: " . $conexao->connect_error);
    }
?>