<?php

$loginState = "ogin"; // Definir o estado inicial como "ogin" (login)
$showDashboard = false; //Na entrega 1 tinha lógica usar esta variável pois o hóspede não tinha acesso à dashboard
// agora na segunda entrega o hóspede possui a sua própria dashboard

//Se fizer login o estado passa para "ogout" (logout)
if (isset($_SESSION["username"])) {
    $loginState = "ogout";
}

//Botão de login/logout foi clicado?
if (isset($_GET['login_logout'])) {
    if ($loginState == "ogin") {
        // Se não tiver autenticado, redireciona para login
        header("Location: login.php");
        exit;
    } elseif ($loginState == "ogout") {
        //Se tiver autenticado, redireciona para logout
        header("Location: logout.php");
        exit;
    }
}
//Variável para esconder barra lateral nas páginas onde não é necessária
if (isset($_GLOBAL['hideSidebarButton']) and $_GLOBAL['hideSidebarButton'] == true) {
    $cssHide = 'none';
} else {
    $cssHide = 'inline';
}

//Lógica para abrir a barra
if (isset($_POST['abrir_barra'])) { //Clicar na barra 
    $abrir_barra = $_POST['abrir_barra']; //$abrir_barra = value do form
} else { // se não clicar, por omissão é falso
    $abrir_barra = 'false';
}

//Se tiver permissão pode aceder à dashboard
if (isset($_SESSION['perm']) && $_SESSION['perm'] >= 0) {
    $showDashboard = true;
}
?>

<nav class="navbar">
    <div class="navbar-left">
        <form id="form_abrir_barra" action="#" method="POST" style="display: <?php echo $cssHide ?>;">
            <input type="hidden" name="abrir_barra" value="<?php
                                                            if ($abrir_barra == 'true') {
                                                                echo 'false';
                                                            } else {
                                                                echo 'true';
                                                            } ?>">
            <button class="btn-sidebar" type="submit"><img src="img/icoMenu.png" width="25" alt=""></button>
        </form>
        <img style="margin-left:20px;" width="60" src="img/logo.png" alt="">
        <span class="navbar-title">Infinity Hotel</span>
    </div>

    <div class="navbar-right">
        <p class="navbar-user">
            <?php
            if (isset($_SESSION["username"]) and isset($_SESSION["perm"])) {
                echo $_SESSION["username"];
                if ($_SESSION["perm"] == 0) {
                    echo " (Hósp)"; //Hóspede
                } elseif ($_SESSION["perm"] == 1) {
                    echo " (Func)"; //Funcionário
                } else {
                    echo " (Adm)"; //Admin
                }
            } ?>
        </p>
        <a href="index.php" class="navbar-link">Home</a>
        <?php
        if ($showDashboard == true) { //Se a dashboard estiver a True mostra opção de ir para dashboard
            echo "<a href=\"dashboard.php\" class=\"navbar-link\">Dashboard</a> ";
        }
        ?>

        <form action="#" method="GET">
            <button type="submit" name="login_logout" class="navbar-login">L<?php echo $loginState ?></button>
        </form>
    </div>
</nav>

<div class="<?php echo $abrir_barra === 'true' ? 'sidebar-open' : 'sidebar-close'; ?>">
    <!-- ao clicar sobre o X, estamos a submeter o form que controla a barra, e consequentemente a fechar a barra -->
    <span class="close-btn" onclick="document.getElementById('form_abrir_barra').submit();">x</span>
    <!-- Se a barra estiver aberta mostra o seu conteudo -->
    <?php if ($abrir_barra == 'true') { ?>
        <a href="dashboard.php"><i class="lni lni-dashboard"></i> Painel</a>
        <a href="historicoAll.php"><i class="lni lni-hourglass"></i> Histórico</a>
        <a href="graficoLotacao.php"><i class="lni lni-graph"></i> Gráficos</a>
    <?php } ?>
</div>