<?php

session_start(); #Iniciar a sessão

#Limpar o buffer de saída
ob_start();

#Incluir a conexão com banco de dados
include_once "connection.php";

#Receber o arquivo do formulário
$file = $_FILES['arquivo'];
//  var_dump($file);
//  exit;
#Variáveis de validação
$first_line = true;
$imported_lines = 0;
$lines_not_imported = 0;
$users_not_imported = "";

#Verificar se é arquivo csv
if($file['type'] == "text/csv"){

    $query_truncat = "TRUNCATE TABLE table_import;";
    $truncat = $conn->prepare($query_truncat);
    #Executar a QUERY para limpar tabela
    $truncat->execute();

    #Ler os dados do arquivo
    $data_file = fopen($file['tmp_name'], "r");

    #Percorrer os dados do arquivo
    while($line = fgetcsv($data_file, 1000, ";")){

        #Como ignorar a primeira linha do Excel
        if($first_line){
            $first_line = false;
            continue;
        }

        #Usar array_walk_recursive para criar função recursiva com PHP
        #array_walk_recursive($line, 'converter');
        #var_dump($line);

        #Criar a QUERY para salvar o usuário no banco de dados
        $query_user = "INSERT INTO table_import (id, name, cpf, date_register) VALUES (:id, :nome, :cpf, :data);";

        #Preparar a QUERY
        $register_user = $conn->prepare($query_user);

        #Substituir os links da QUERY pelos valores
        $register_user->bindValue(':id', ($line[0] ?? "NULL"));
        $register_user->bindValue(':nome', ($line[1] ?? "NULL"));
        $register_user->bindValue(':cpf', ($line[2] ?? "NULL"));
        $register_user->bindValue(':data', ($line[3] ?? "NULL"));

        #Executar a QUERY
        $register_user->execute();

        #Verificar se cadastrou corretamente no banco de dados
        if($register_user->rowCount()){
            $imported_lines++;
        }else{
            $lines_not_imported++;
            $users_not_imported = $users_not_imported . ", " . ($line[2] ?? "NULL");
        }
    }

    #Criar a mensagem com os CPF dos usuários não cadastrados no banco de dados
    if(!empty($users_not_imported)){
        $users_not_imported = "Usuários não importados: $users_not_imported.";
    }

    #Mensagem de sucesso
    $_SESSION['msg'] = "<p style='color: green;'>$imported_lines linha(s) importa(s), $lines_not_imported linha(s) não importada(s). $users_not_imported</p>";

    #Redirecionar o usuário
    header("Location: index.php");
}else{

    #Mensagem de erro
    $_SESSION['msg'] = "<p style='color: #f00;'>Necessário enviar arquivo csv!</p>";

    #Redirecionar o usuário
    header("Location: index.php");
}

#Criar função valor por referência, isto é, quando alter o valor dentro da função, vale para a variável fora da função.
function converter(&$dados_arquivo)
{
    #Converter dados de ISO-8859-1 para UTF-8
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}