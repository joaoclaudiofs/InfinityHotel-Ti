<!-- Como não fazemos session start, ninguém vai conseguir abrir esta página pelo url, o que é o pretendido -->
<?php if (!isset($_SESSION['username']) || (!isset($_SESSION['perm']))) {  #verifica se existe algum user autenticado 
    header("refresh:3;url=index.php"); #se nao volta para index.php                                   #verifica se tem permissao
    die('<span style="color:black;font-size:x-large;">Acesso restrito!</span>'); #mata a pagina
} ?>
<script>
    //URL DA API
    const apiUrl = "https://iot.dei.estg.ipleiria.pt/ti/g049/api/api.php?allnome=";
    const dispositivos = [{
        id: 'arcondicionado', //ID DO DISPOSITIVO
        symbol: '', //SIMBOLO A ADICIONAR NO CARD
        img1: 'acOn', //IMAGEM 1 
        img2: 'acOf', //IMAGEM 2 
        img3: null, //IMAGEM 3 
        par1: "Ligado", //ESTADO 1
        par2: null
    }, {
        id: 'estores',
        symbol: '',
        img1: 'estoresOn',
        img2: 'estoresOf',
        img3: null,
        par1: 'Ligado',
        par2: null
    }];

    //FUNÇÃO ASSINCRONA PARA FAZER GET À API 

    async function getData(nome) {
        //Concatena o url da api preparado para o get + o nome passado por parâmetro
        const url = apiUrl + nome;
        try {
            // realiza um fetch
            const response = await fetch(url);
            if (response.ok) {
                // retorna a resposta com nome, valor, data
                const data = await response.json();
                return data;
            }
        } catch (erro) {
            console.error('Erro:', erro);
            return null;
        }
    }

    //FUNCAO ASSINCRONA PARA ATUALIZAR O NOME, VALOR E HORA DOS DISPOSITIVOS
    async function fetchAndUpdate(nome, symbol) {
        //Chama a funcao que obtem os dados atualizados do dispositivo de acordo com o dispositivo especificado
        const data = await getData(nome);
        if (data) {
            const valorElement = document.querySelectorAll(
                `[id*="${nome}_valor"]`); //procura o/os elementos com id {nome}_valor
            const horaElement = document.querySelectorAll(
                `[id*="${nome}_hora"]`); //procura o/os elementos com id {nome}_hora
            const nomeElement = document.querySelectorAll(`[id*="${nome}_nome"]`);
            //procura o/os elementos com id {nome}_nome

            valorElement.forEach(elementId => elementId.innerText = data.valor +
                symbol); //Para cada elemento encontrado escreve o valor e o simbolo 
            horaElement.forEach(elementId => elementId.innerText = data.hora);
            //Para cada elemento encontrado escreve a hora 
            nomeElement.forEach(elementId => elementId.innerText = data.nome);
            //Para cada elemento encontrado escreve o nome
        }
    }

    //FUNCAO ASSINCRONA PARA ATUALIZAR AS IMAGENS DOS CARDS
    async function updateImage(id, value, img1, img2, img3, par1, par2) {
        var img;
        //Se existir imagem 3 e um limite inferior significa que tem 3 imagens, o que corresponde a algo com um valor numérico 
        if (img3 != null && par2 != null) {
            value = parseInt(value)
            if (value >= par1) { // se o valor for superior ao parametro/limite 1 significa que a imagem é a nº1 
                img = "img/" + img1 + ".png";
            } else if (value >= par2) { // se o valor estiver entre o parametro 1 e 2 significa que a imagem é a nº 2
                img = "img/" + img2 + ".png";
            } else { // se o valor estiver abaixo do parametro 2 significa que a imagem é a nº 3
                img = "img/" + img3 + ".png";
            }
        } else { //Se não, significa se só possui 2 imagens  
            if (value.toString() == par1) { // Se o status for igual ao parametro 1 significa que é a imagem nº1
                img = "img/" + img1 + ".png";
            } else { // Se não, significa que é a imagens nº2
                img = "img/" + img2 + ".png";
            }
        }
        document.getElementById(id + '_img').src = img; //procura o elemento com o {id}_img e atualiza
    }


    //FUNCAO ASSINCRONA PARA ATUALIZAR OS AVISOS DA TABELA
    async function updateAviso(id, value, par1, par2) {
        let classe; //variavel para a classe
        let aviso; //variavel para o aviso
        if (par2 != null) { //se existir parametro 2 
            value = parseInt(value) // é um valor numerico
            if (value >= par1) { //se o valor for superior ao par1
                classe = "alto";
                aviso = "Alto";
            } else if (value >= par2) { //se o valor estiver entre [par2, par1]
                classe = "normal";
                aviso = "Normal";
            } else { //se o valor for abaixo do par2
                classe = "baixo";
                aviso = "Baixo";
            }
        } else {
            if (value.toString() === "Fogo") { //Exceção para o sensor de fumo, continuará ativo mesmo com fogo
                classe = "ativo";
                aviso = "Ativo(FOGO)";
            } else if (value.toString() ===
                "Normal") { //Exceção para o sensor de fumo
                classe = "ativo";
                aviso = "Ativo";
            } else if (value.toString() === par1.toString()) { //Se o valor for igual ao par1, está ativo
                classe = "ativo";
                aviso = "Ativo";
            } else { // Se não significa que não está ativo
                classe = "desativado";
                aviso = "Não ativo";
            }
        }
        avisoElement = document.getElementById(id + '_aviso'); //Procura o elemento com id {id}_aviso
        avisoElement.className = classe; //Atualiza a sua classe
        avisoElement.innerText = aviso; //Atualiza o aviso
    }



    //FUNÇÃO ASSINCRONA PARA OBTER o VALOR A PARTIR DO ID
    async function getValue(id) {
        return document.getElementById(id + '_valor').innerText;
    }

    //FUNCAO QUE ATUALIZA TODO O CARD DO DISPOSITIVO
    async function updateData(d) {
        // Atualizar os dados do dispositivo d
        fetchAndUpdate(d.id, d.symbol);
        const value = await getValue(d.id); // Obter o valor do dispositivo d
        // Atualizar a imagem do dispositivo d 
        updateImage(d.id, value, d.img1, d.img2, d.img3, d.par1, d.par2);
        // Atualizar a imagem do dispositivo d 
        updateAviso(d.id, value, d.par1, d.par2);
        //Atualizar a cada 1 segundo
        setTimeout(() => updateData(d), 1000);
    }

    //FUNCAO QUE ATUALIZA TODOS OS DISPOSITIVOS 
    async function startFetching() {
        for (const d of dispositivos) {
            await updateData(d);
        }
    }
    //Quando o documento tiver sido carregado completamente
    document.addEventListener('DOMContentLoaded', async (event) => {
        //Ao ser carregado o DOM queremos que apareça logo as informações por isso percorremos os dispositivos
        for (const d of dispositivos) {
            //Atualiza o conteúdo 
            await fetchAndUpdate(d.id, d.symbol);
            //Obtem o valor
            const value = await getValue(d.id);
            //Atualiza Imagens
            updateImage(d.id, value, d.img1, d.img2, d.img3, d.par1, d.par2);
            // Atualiza avisos
            updateAviso(d.id, value, d.par1, d.par2);
        }
        startFetching(); // começa o fetching, que tem funcao com ciclo de repeticao
    });
</script>


<!-- Atuadores -->
<div class="row">
    <div class="meu_cartao col-md bg-danger bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <!-- Trocar o nome (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_nome  -->
                <strong id="arcondicionado_nome"></strong>
            </div>
            <div class="imagem card-body">
                <!-- Trocar a imagem (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_img  -->
                <img id="arcondicionado_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <!-- Trocar o valor (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_valor  -->
                    <span id="arcondicionado_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <!-- Trocar o valor (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_hora  -->
                    <span id="arcondicionado_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <!-- GET com nome do dispositivo para mostrar historico -->
                <a class="btn btn-primary" href="historico.php?nome=arcondicionado">Histórico</a>
            </div>
        </div>
    </div>

    <div class="meu_cartao col-md bg-danger bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="estores_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="estores_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="estores_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="estores_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=estores">Histórico</a>
            </div>
        </div>
    </div>
</div>



<!-- Tabela geral de informações -->
<div class="row">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Dispositivo</th>
                <th>Valor</th>
                <th>Aviso</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- todos os dispositivos seguem a mesma nomenclatura para serem atualizados pelas funções JS acima -->
                <td id="arcondicionado_nome"></td>
                <td id="arcondicionado_valor"></td>
                <td id="arcondicionado_aviso"></td>
            </tr>
            <tr>
                <td id="estores_nome"></td>
                <td id="estores_valor"></td>
                <td id="estores_aviso"></td>
            </tr>
        </tbody>
    </table>
</div>