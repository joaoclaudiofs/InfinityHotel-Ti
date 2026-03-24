<?php

header('Content-Type: text/html; charset=utf-8');

// Método POST para receber um ficheiro de imagem
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['imagem'])) {
        //Tipo de ficheiro permitidos
        $tiposPermitidos = ['image/jpeg', 'image/png'];
        //Tipo do ficheiro enviado
        $tipoAtual = $_FILES['imagem']['type'];
        //Se o tipo do ficheiro não tiver nos tipos permitidos não permite enviar
        if (!in_array($tipoAtual, $tiposPermitidos)) {
            http_response_code(400);
            echo "Apenas aceito imagens em JPEG ou PNG!";
            return;
        }

        //Tamanho do ficheiro 1000KB = 1000 * 1024 pois 1KB = 1024 B
        $tamMax = 1000 * 1024;
        //Tamanho do ficheiro atual
        $tamAtual = $_FILES['imagem']['size'];
        //Se o tamanho for superior ao maximo não permite enviar
        if ($tamAtual > $tamMax) {
            http_response_code(400);
            echo "O tamanho máximo das imagens é 1000KB!";
            return;
        }
        //Buscar a data atual num formato compatível com o nome de um ficheiro
        $timestamp = date('Y-m-d-H-i-s');
        //Nome da imagem
        $filename = "webcam_$timestamp.jpg";
        //Pasta dos logs das imagens
        $logPath = "images/log/$filename";
        //Pasta da imagem que é mostrada
        $showPath = "images/webcam.jpg";

        // Move a imagem para a pasta de logs
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $logPath)) {
            // Copia a imagem para a pasta de visualização (que terá sempre apenas a última imagem processada)
            if (copy($logPath, $showPath)) {
                //Atualiza data novamente, mas aora num formato para ser mostrado
                $timestamp = date('Y-m-d H:i:s');
                file_put_contents("images/last_upload.txt", $timestamp); //Guardar a data da última imagem
                echo "Upload efetuado! Imagem guardada como $filename"; //Mensagem de sucesso
            } else { // ERRO ao copiar
                http_response_code(500);
                echo "Erro ao copiar a imagem!";
            }
        } else { //ERRO ao mover
            http_response_code(500);
            echo "Erro ao mover a imagem!";
        }
    } else {
        http_response_code(400);
        echo "Faltam parâmetros no POST!";
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['imagemAtual'])) {
        // Buscar a imagem atual
        $showPath = "images/webcam.jpg";
        if (file_exists($showPath)) { //Se existir lê a imagem
            header('Content-Type: image/jpeg');
            readfile($showPath);
        } else { //Se não encontrar a imagem 
            http_response_code(404);
            echo "Imagem atual não encontrada!";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['imagemData'])) { //Vai buscar a data da imagem atual
        $dataPath = "images/last_upload.txt"; //Path da data
        if (file_exists($dataPath)) {
            echo file_get_contents($dataPath); //Envia a data
        } else { //Não encontrou a data
            http_response_code(404);
            echo "Data não encontrada!";
        }
    } else {
        http_response_code(400);
        echo "Faltam parâmetros no GET!";
    }
} else {
    http_response_code(403); //Método diferente de GET ou POST
    echo "Recebi um método não permitido!";
}
