<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Hotel - Gráfico</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
    <link rel="stylesheet" href="css/historico.css">
    <link rel="stylesheet" href="css/style.css">
    <script src='https://cdn.plot.ly/plotly-2.32.0.min.js'></script>
</head>

<body>
    <header>
        <?php include('menu.php'); ?>
    </header>
    <main>
        <!-- Verifica se  o nome permissao estao definidos e se a permissao nao for admin (!=2) nao pode aceder -->
        <?php if (!isset($_SESSION['username']) || (!isset($_SESSION['perm'])) || (isset($_SESSION['perm']) && $_SESSION['perm'] != 2)) {
            header("refresh:3;url=index.php");
            die('<span style="color:white;font-size:x-large;">Acesso restrito!</span>');
        } ?>
        <div class="container">
            <div class="content">
                <div class="">
                    <!-- Div onde o gráfico vai ser desenhado -->
                    <div id="grafico""></div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            //    Faz fetch aos logs da lotação
        
        fetch('api/files/lotacao/log.txt')
            .then(response => response.text()) // Converte a resposta para texto
            .then(text => {
                // Divide o texto do log, juntado o conteudo em linhas, separadas pelo \n e guarda numa variavel
                const linhas = text.trim().split('\n');
                const x = [];
                const y = [];
            linhas.forEach(line => { //Percorre as linhas
                    const [data, valor] = line.split(';'); //Separa a data e o valor pelo ; e guarda em variáveis
                    x.push(data); //adiciona a data ao final do array dos x 
                    y.push(parseInt(valor)); //converte o valor para inteiro e adiciona o valor ao final do array dos y 
            });
                //Traços do gráfico
                const trace = {
                    x: x, //definir o eixo dos x
                    y: y, // definir o eixo dos y
                    type: 'scatter', //Tipo scatter
                    mode: 'lines+markers', //Modo com linha e pontos
                    marker: { color: 'rgba(150, 100, 255, 0.5)' }, //Cor dos Pontos 
                    line: { color: ' rgba(150, 200, 255, 1)' } //Cor da Linha
                };
                //Layot
                const layout = {
                    //Titulo
                    title: 'Lotação do Hotel',
                    xaxis: {
                        //Eixo x
                        title: 'Data', // Titulo
                        showgrid: false, // Não mostrar grades no eixo x
                        zeroline: false, // Não mostrar a linha zero no eixo x
                        type: 'category', // Tipo do eixo x 
                        tickangle: -45 //Ângulo das labels
                    },
                    yaxis: {
                        //Eixo x
                        title: 'Lotação', //Titulo
                        showline: false //Não mostrar a linha do eixo y
                    },
                    margin: { //Margens
                        l: 50, 
                        r: 50, 
                        b: 150,
                        t: 50, 
                    },
                    autosize: true,  //Auto ajustamento
                    height: 500  //Altura
                };

                Plotly.newPlot('grafico', [trace], layout); //Criar o gráfico 
            })
            .catch(error => console.error('Erro:', error)); // Aconteceu um erro
    });
    </script>
    <script src=" https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
                        </script>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous">
                        </script>
</body>

</html>