<?php

$_GLOBAL['hideSidebarButton'] = true; //Esconder barra lateral

$csstext = '"display: none; visibility: hidden;"'; //Classe do texto de alerta (invisivel inicialmente)
$alert = "";


//Se tiver preenchido ambos os campos e submetido o form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['txtUsername'], $_POST['txtPass'])) {
    //Há algum que foi selecionado?
    if (isset($_POST['checkAdmin']) || isset($_POST['checkFunc']) || isset($_POST['checkHosp'])) {
        //Quantos checks estão selecionados?
        $selectedCount = isset($_POST['checkAdmin']) + isset($_POST['checkHosp']) + isset($_POST['checkFunc']);

        //Apenas um campo foi selecionado?
        if ($selectedCount === 1) {
            // Define $perm de acordo com a checkbox
            if (isset($_POST['checkAdmin'])) {
                $perm = 2; // Se checkAdmin está selecionado, atribui 2 a $perm
            } elseif (isset($_POST['checkFunc'])) {
                $perm = 1; // Se checkFunc está selecionado, atribui 1 a $perm
            } elseif (isset($_POST['checkHosp'])) {
                $perm = 0; // Se checkHosp está selecionado, atribui 0 a $perm
            }
            $username = $_POST['txtUsername'];
            $password = $_POST['txtPass'];
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $file = fopen('logincredenciais.txt', 'a+'); //abre o ficheiro em modo de acréscimo
            if ($file) {
                //escreve o user a hash e a permissão dele
                fwrite($file, $username . ':' . $password_hash . ':' . $perm . PHP_EOL);
                $csstext = "display: block; visibility: visible;";
                $alert = "Username criado com sucesso.";
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['perm'] = $perm;
                fclose($file);
                if ($_SESSION['perm'] > -1) {
                    header("refresh:0;url=dashboard.php"); // Redireciona para a página de dashboard
                } else {
                    header("refresh:0;url=index.php"); // Redireciona para a página inicial
                }
            } else {
                $csstext = "display: block; visibility: visible;";
                $alert = "Erro ao aceder ao ficheiro de credenciais.";
            }
        } else {
            // Mais de um campo selecionado
            $csstext = "display: block; visibility: visible;";
            $alert = "Por favor, selecione apenas uma opção.";
        }
    } else {
        // Nenhum campo foi selecionado
        $csstext = "display: block; visibility: visible;";
        $alert = "Por favor, selecione uma opção.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Registo</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="css/register.css">
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
                            <h3 class="mb-2 text-center pt-5 register-color">Registar</h3>
                            <p class="text-center lead register-color">Bem-vindo ao Infinity Hotel</p>
                            <!-- Form com método POST para enviar os dados -->
                            <form action="#" method="POST">
                                <!-- Username -->
                                <label class="field-text" for="txtUsername">Username</label>
                                <input name="txtUsername" id="txtUsername" class="form-control mb-3" required>
                                <!-- Password -->
                                <label class="field-text" for="txtPass">Password</label>
                                <input name="txtPass" id="txtPass" class="form-control" type="password" required>
                                <br>
                                <!-- Checkboxes para o tipo de utilizador -->

                                <!-- Admin -->
                                <div class="form-check checkbox">
                                    <input class="form-check-input" type="checkbox" value="" name="checkAdmin" id="checkAdmin" />
                                    <label class="form-check-label" for="">Admin</label>
                                </div>
                                <!-- Funcionário -->
                                <div class="form-check  checkbox">
                                    <input class="form-check-input" type="checkbox" value="" name="checkFunc" id="checkFunc" />
                                    <label class="form-check-label" for="">Funcionário</label>
                                </div>
                                <!-- Hóspede -->
                                <div class="form-check  checkbox">
                                    <input class="form-check-input" type="checkbox" value="" name="checkHosp" id="checkHosp" />
                                    <label class="form-check-label" for="">Hóspede</label>
                                </div>
                                <button class="btn btn-lg w-100 shadow-lg mt-4 register-button">Registar</button>
                            </form>
                            <br>
                            <p class="register-color">Possui conta? Faça login abaixo</p>
                            <!-- Reencaminhar para o login -->
                            <a href="login.php">Área de Login</a>
                            <div style=<?php echo $csstext ?> class="mt-1 alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Erro!</strong> <?php echo $alert ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
</body>

</html>