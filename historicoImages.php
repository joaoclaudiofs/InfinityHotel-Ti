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
    <!-- <link rel="stylesheet" href="css/historico.css">  -->
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="15">

    <style>
        body {
            background-color: #254a62;
        }

        h1 {
            color: white;
        }

        .title {
            font-size: large;
        }

        .date {
            font-size: medium;
        }
    </style>
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
                <br>
                <div class="row">
                    <a href="dashboard.php" style="font-size: 20px; color: white; text-decoration: none; margin-bottom:30px">
                        <span style="font-size: 30px;">&larr;</span> Voltar
                    </a>
                    <br>
                    <h1>Histórico - Imagens
                        <div class="table-responsive" style="margin-top: 15px;">
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th scope="col" class="title">Imagem</th>
                                        <th scope="col" class="title">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Pasta do log das imagens
                                    $logDirectory = "api/images/log/";

                                    // Verifica se a pasta existe
                                    if (is_dir($logDirectory)) {
                                        // Obtem todos os files da pasta e coloca-os num array
                                        $files = scandir($logDirectory);
                                        // Inverte a ordem para começar do mais recente
                                        $files = array_reverse($files);

                                        foreach ($files as $file) {
                                            // Verifica se o file é uma imagem 
                                            $extension = pathinfo($file, PATHINFO_EXTENSION);

                                            if ((in_array($extension, array('jpg', 'jpeg', 'png', 'gif')))) {
                                                // Obtem a data da imagem
                                                $filePath = $logDirectory . $file;
                                                $date = date("Y-m-d H:i:s", filemtime($filePath));
                                    ?>
                                                <tr>
                                                    <!-- Imprime imagem e a data -->
                                                    <td><img style="border-radius: 10px;" src='<?php echo $filePath; ?>' alt='Imagem' width='300px'></td>
                                                    <td class="date"><?php echo $date; ?></td>
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
                <br>
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