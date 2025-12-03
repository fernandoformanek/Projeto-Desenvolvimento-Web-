<?php
    $dbhost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'db_site';

    $conexao = new mysqli($dbhost,$dbUsername,$dbPassword,$dbName);

    if($conexao->connect_errno)
    {
       die("ERRO FATAL NA CONEXÃO: " . $conexao->connect_error);
    }
?>