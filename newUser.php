<?php

//[DEPRECATED - versão atualizada => register.php]
//fazer http://localhost/**Pasta**/newUser.php para executar 

/*
$newUser = "Tiago";
$password = "1234";
$admin = false;

//cria hash 
$password_hash = password_hash($password, PASSWORD_DEFAULT);


$file = fopen('logincredenciais.txt', 'a+');


if ($file) {

    //escreve o user a hash e a permissão dele
    fwrite($file, $newUser . ':' . $password_hash . ':' . $admin . PHP_EOL);


    fclose($file);

    echo "Sucesso!";
} else {
    echo "Erro!";
}
*/