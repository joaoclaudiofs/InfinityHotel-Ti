<?php

$_GLOBAL['hideSidebarButton'] = true; //Esconder barra lateral

$csstext = '"display: none; visibility: hidden;"'; //Classe do texto de alerta (invisivel inicialmente)
$alert = "";

//Se tiver preenchido ambos os campos e submetido o form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['txtUsername'], $_POST['txtPass'])) {
    $username = $_POST['txtUsername'];
    $password = $_POST['txtPass'];

    // Abrir o ficheiro txt de credenciais
    $file = fopen('logincredenciais.txt', 'r');
    if ($file) {
        while (!feof($file)) { //enquanto não ao fim do ficheiro
            $line = fgets($file); //obtê uma linha de cada vez 
            $credenciais = explode(':', $line); //divide a linha pelo :
            if (count($credenciais) == 3) { // certificamo-nos de que existem três partes na linha (user:pass_hash:admin) admin 1 ou 0
                $user = trim($credenciais[0]);  //trim remove espaços que possam existir a mais
                $password_hash = trim($credenciais[1]);
                $perm = trim($credenciais[2]);
                //Verificamos se o user e a password correspondem
                if ($user == $username && password_verify($password, $password_hash)) {
                    session_start();
                    $_SESSION['username'] = $username;
                    $_SESSION['perm'] = $perm;
                    fclose($file);
                    //Se for admin (2), funcionário(1) ou hóspede (0)
                    if ($_SESSION['perm'] > -1) {
                        header("refresh:0;url=dashboard.php"); // Redireciona para a página de dashboard
                    } else { // Se nao tiver permissao associada 
                        header("refresh:0;url=index.php"); // Redireciona para a página inicial
                    }
                    exit();
                }
            }
        }
        fclose($file);
        $csstext = "display: block; visibility: visible;";
        $alert = "Username ou Password Incorretos.";
    } else {
        $csstext = "display: block; visibility: visible;";
        $alert = "Erro ao aceder ao ficheiro de credenciais.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Autenticação</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <?php include('menu.php'); ?>
    </header>
    <main>
        <div class="center-container">
            <div class="container">
                <div class="row">

                    <div class="col-lg-6 col-12 mx-auto mt-5">
                        <div class="text-center image-size-small position-relative">
                            <img src="img/loginface.png" alt="#" class="rounded-circle p-2 bg-white">
                        </div>
                        <div class="p-5 rounded shadow-lg rectangule-color">
                            <h3 class="mb-2 text-center pt-5 login-color">Entrar</h3>
                            <p class="text-center lead login-color">Efetue a sua autenticação</p>
                            <!-- Form com método POST para enviar o username e password ao ser submetido -->
                            <form action="#" method="POST">
                                <label class="field-text" for="txtUsername">Username</label>
                                <input name="txtUsername" id="txtUsername" class="form-control mb-3" required>

                                <label class="field-text" for="txtPass">Password</label>
                                <input name="txtPass" id="txtPass" class="form-control" type="password" required>
                                <button class="btn btn-lg w-100 shadow-lg mt-4 login-button">Entrar</button>
                            </form>
                            <br>
                            <p class="login-color">Não possui conta? Registe-se abaixo</p>
                            <!-- Reencaminhar para a página de registo -->
                            <a href="register.php">Área de Registo</a>
                            <div style=<?php echo $csstext ?>
                                class="mt-1 alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Erro!</strong> <?php echo $alert ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
</body>

</html>