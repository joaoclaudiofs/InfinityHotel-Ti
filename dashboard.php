<?php

session_start();

?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Hotel - Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/painel.css">
</head>

<body>
    <header>
        <?php include('menu.php'); ?>
    </header>
    <main>
        <?php if (!isset($_SESSION['username']) || (!isset($_SESSION['perm']))) {  #verifica se existe algum user autenticado 
            header("refresh:3;url=index.php"); #se nao volta para index.php                                   #verifica se tem permissao
            die('<span style="color:white;font-size:x-large;">Acesso restrito!</span>'); #mata a pagina
        } ?>
        <div class="container">
            <div class="row">
                <div class="col-md">
                    <div class="content">
                        <h1>Dashboard</h1>
                        <?php
                        //Admin ou funcionário tem uma dashboard diferente do hóspede, estes podem ver todos os dispositivos
                        if ($_SESSION['perm'] > 0) {
                            include('painel.php');
                        } else {
                            //Hóspede pode ver apenas os dispositivos do seu quarto
                            include('painelHospede.php');
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