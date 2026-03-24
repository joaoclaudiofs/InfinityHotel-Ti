<?php

session_start();   //diz ao browser para utilizar variáveis de sessão;

session_unset();   // remove todas as variáveis de sessão;

session_destroy();  // destrói a sessão;


header("Location:index.php");
exit; //certificar 