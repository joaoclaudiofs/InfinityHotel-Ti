<?php session_start(); ?>
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
        <!-- Verifica se  o nome permissao estao definidos e se a permissao nao for admin (!=2) nao pode aceder -->
        <?php
        if (!isset($_SESSION['username']) || (!isset($_SESSION['perm'])) || (isset($_SESSION['perm']) && $_SESSION['perm'] != 2)) {
            header("refresh:3;url=index.php");
            die('<span style="color:white;font-size:x-large;">Acesso restrito!</span>');
        }
        ?>
        <div class="container">
            <div class="content">
                <div class="row">
                    <a href="dashboard.php" style="font-size: 20px; color: white; text-decoration: none; margin-bottom:30px">
                        <span style="font-size: 30px;">&larr;</span> Voltar
                    </a>

                    <h1>Histórico - <?php
                                    // se o nome está definido
                                    if (isset($_GET['nome'])) {
                                        $nome = $_GET['nome'];
                                        $path = "api/files/" . $nome . "/nome.txt";
                                        if (file_exists($path)) { // verifica se o ficheiro com o nome existe
                                            echo file_get_contents($path); //imprime o nome do dispositivo 
                                        } else {
                                            die("Dispositivo não existe!!");
                                        }
                                    } else {
                                        die("Nada foi especificado GET!");
                                    } ?></h1>
                    <div class="scroll">
                        <table class="table table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $max = 5; //maximo da paginas na paginacao
                                $limit = 20; //Limite linhas
                                //a página atual vai ser definida por um get
                                if (isset($_GET['page'])) {
                                    $currentPage = $_GET['page'];
                                } else {
                                    $currentPage = 1;
                                }
                                //-1 porque a contagem começa no 0 
                                // * limit para saber a posição dos elementos a mostrar de cada pagina
                                $offset = ($currentPage - 1) * $limit;
                                //vamos ver se o histórico do valor mandado por get existe
                                if (isset($_GET['nome'])) {
                                    $nome = $_GET['nome'];
                                    $path = "api/files/" . $nome . "/log.txt";

                                    if (file_exists($path)) {
                                        //se existir obtemos o conteúdo
                                        $file = file_get_contents($path);
                                        //obtemos um array só com as linhas / fazemos a divisão pelos espaços (\n)
                                        $lines = explode("\n", $file);
                                        //O ficheiro escreve de cima para baixo e queremos ler de baixo para cima
                                        $lines = array_reverse($lines);
                                        //guardamos o numero total de linhas
                                        $totalLines = count($lines);
                                        //guardamos o numero total de paginas
                                        $totalPages = ceil($totalLines / $limit);
                                        //o começo vai ser a página-1 * limit
                                        $start = ($currentPage - 1) * $limit;
                                        //o fim será o menor valor entre a soma do indice inicial da linha com o limite por pagina e o total de linhas
                                        // assim garante caso o último conjunto de linhas da página atual ultrapasse o total de linhas do ficheiro não vai haver erro por aceder a uma linha q nao existe
                                        $end = min($start + $limit, $totalLines);
                                        for ($i = $start; $i < $end; $i++) {
                                            //Trim para remover espaços que possam existir a mais
                                            $line = trim($lines[$i]);
                                            // Ignora as linhas vazias e os comentários
                                            if ((empty($line)) || (preg_match('/^#/', $line) > 0))
                                                continue;
                                            //dividir a linha pelo ; e guardar em 2 variaveis
                                            list($data, $valor) = explode(';', $line);
                                            $data = trim($data);
                                            $valor = trim($valor);
                                            $numeroLog = $i;
                                ?>
                                            <tr>
                                                <!-- Imprime o numero da linha, a data e o valor -->
                                                <th scope="row"><?php echo $numeroLog; ?></th>
                                                <td><?php echo $data; ?></td>
                                                <td><?php echo $valor; ?></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                        <?php
                        // Se houver mais de uma página, exibe a paginação
                        if ($totalPages > 1) {
                            echo '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

                            // Botão da anterior
                            if ($currentPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '&nome=' . $nome . '">Anterior (' . ($currentPage - 1) . ')</a></li>';
                            }

                            // vai iterando as páginas
                            for ($i = 1; $i <= min($max, $totalPages); $i++) {
                                echo '<li class="page-item';
                                // atualiza o numero de pagina ativa
                                if ($i == $currentPage) {
                                    echo ' active';
                                }
                                // coloca um link para o numeor da pagina onde clicar
                                echo '"><a class="page-link" href="?page=' . $i . '&nome=' . $nome . '">' . $i . '</a></li>';
                            }
                            // Botão da próxima
                            if ($currentPage < $totalPages) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '&nome=' . $nome . '">Próxima (' . ($currentPage + 1) . ')' . '/' . $totalPages . '</a></li>';
                            }

                            echo '</ul></nav>';
                        }
                        ?>

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