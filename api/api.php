<?php

header('Content-Type: text/html; charset=utf-8');


//Método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['valor'], $_POST['hora'], $_POST['nome'])) {
        $path = "files/" . $_POST['nome'];
        if (file_exists($path)) {
            print_r($_POST);
            // Coloca o conteúdo do post nos respetivos ficheiros
            file_put_contents("files/" . $_POST['nome'] . "/valor.txt", $_POST['valor']);
            file_put_contents("files/" . $_POST['nome'] . "/hora.txt", $_POST['hora']);
            file_put_contents("files/" . $_POST['nome'] . "/log.txt", $_POST['hora'] . ";" . $_POST['valor'] . PHP_EOL, FILE_APPEND);
        } else {
            http_response_code(400);
            echo "Parâmetros errados no POST!!";
        }
    } else {
        http_response_code(400);
        echo "Faltam parâmetros no POST!!";
    }
    //Método GET
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //Devolver valor de um dispositivo
    if (isset($_GET['nome'])) {
        $path = "files/" . $_GET['nome'] . "/valor.txt";
        if (file_exists($path)) {
            echo file_get_contents($path);
        } else {
            http_response_code(400);
            echo "Parâmetros errados no GET!!";
        }
        //Devovler histórico de um dispositivo
    } else if (isset($_GET['lognome'])) {
        $path = "files/" . $_GET['lognome'] . "/log.txt";
        if (file_exists($path)) {
            echo file_get_contents($path);
        } else {
            http_response_code(400);
            echo "Parâmetros errados no GET!!";
        }
    } else if (isset($_GET['allnome'])) {
        // Este método devolve informações atualizadas sobre o dispositivo -> valor, hora, e nome , ideal para atualizar o conteúdo da dashboard
        $basePath = "files/" . $_GET['allnome'] . "/";
        // Valor
        $valorPath = $basePath . "valor.txt";
        // Hora
        $horaPath = $basePath . "hora.txt";
        // Nome 
        $nomePath = $basePath . "nome.txt";
        if (file_exists($valorPath) && file_exists($horaPath) && file_exists($valorPath)) { //Se existirem
            echo json_encode([
                "valor" => file_get_contents($valorPath),
                "hora" => file_get_contents($horaPath),
                "nome" => file_get_contents($nomePath)
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Parâmetros errados no GET!!"]);
        }
    } else {
        http_response_code(400);
        echo "Faltam parâmetros no GET!!";
    }
} else {
    http_response_code(403); // Método diferente de POST e GET
    echo "Recebi um método não permitido!\n";
}
