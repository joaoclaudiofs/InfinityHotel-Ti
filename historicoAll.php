<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Hotel - Histórico</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="css/historico.css">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="15">
</head>

<body>
    <header>
        <?php include('menu.php'); ?>
    </header>
    <main>
        <?php if (!isset($_SESSION['username']) || (!isset($_SESSION['perm'])) || (isset($_SESSION['perm']) && $_SESSION['perm'] != 2)) {  #verifica se existe algum user autenticado 
            header("refresh:3;url=index.php"); #se nao volta para index.php                                   #verifica se não é admin
            die('<span style="color:white;font-size:x-large;">Acesso restrito!</span>'); #mata a pagina
        } ?>
        <div class="container">
            <div class="content">
                <div class="row">
                    <a href="dashboard.php" style="font-size: 20px; color: white; text-decoration: none; margin-bottom:30px">
                        <span style="font-size: 30px;">&larr;</span> Voltar
                    </a>
                    <h1>Histórico</h1>
                    <div class="scroll">
                        <table class="table table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Dispositivo</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $numeroLog = 0;
                                $arrayLogs = array(); //array que guarda todos os logs 
                                // https://www.php.net/manual/en/function.glob.php
                                //função glob -> encontra caminhos que correspondam a um padrão
                                //parametro GLOB_ONLYDIR -> retorna apenas diretórios que correspondam ao pedido
                                $pastas = glob("api/files/*", GLOB_ONLYDIR);
                                foreach ($pastas as $pasta) {
                                    $nome = basename($pasta); //nome da pasta
                                    $path = "api/files/" . $nome . "/nome.txt";
                                    $dispositivo = file_get_contents($path); // buscar nome do dispositivo
                                    $log = $pasta . "/log.txt";
                                    // se o ficheiro log existir extraímos o seu conteúdo
                                    if (file_exists($log)) {
                                        $lines = file($log);
                                        //Vamos buscar as últimas três linhas
                                        $ultimasLinhas = array_slice($lines, -3);
                                        //O ficheiro escreve de cima para baixo e queremos ler de baixo para cima
                                        $ultimasLinhas = array_reverse($ultimasLinhas);
                                        foreach ($ultimasLinhas as $line) {
                                            if ((empty(trim($line))) || (preg_match('/^#/', $line) > 0))
                                                continue; //se entrar aqui ele volta para o inicio do foreach
                                            //Trim para remover espaços que possam existir a mais
                                            $line = trim($line);
                                            //dividir a linha pelo ; e guardar em 2 variaveis
                                            list($data, $valor) = explode(';', $line);
                                            $data = trim($data);
                                            $valor = trim($valor);
                                            $numeroLog += 1;
                                ?>
                                            <tr>
                                                <!-- Imprime o numero, nome, data e valor -->
                                                <th scope="row"><?php echo $numeroLog; ?></th>
                                                <td><?php echo $dispositivo ?></td>
                                                <td><?php echo $data ?></td>
                                                <td><?php echo $valor ?></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
</body>

</html>