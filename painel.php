<!-- Como não fazemos session start, ninguém vai conseguir abrir esta página pelo url, o que é o pretendido -->
<?php if (!isset($_SESSION['username']) || (!isset($_SESSION['perm']))) {  #verifica se existe algum user autenticado 
    header("refresh:3;url=index.php"); #se nao volta para index.php                                   #verifica se tem permissao
    die('<span style="color:black;font-size:x-large;">Acesso restrito!</span>'); #mata a pagina
} ?>

<script>
    //URL DA API
    const apiUrl = "https://iot.dei.estg.ipleiria.pt/ti/g049/api/api.php?allnome=";
    //URL DO UPLOAD
    const apiUrlImg = "https://iot.dei.estg.ipleiria.pt/ti/g049/api/upload.php";
    // ARRAY DE DISPOSITIVOS
    const dispositivos = [{
            id: 'temperatura', //ID DO DISPOSITIVO
            symbol: 'ºC', //SIMBOLO A ADICIONAR NO CARD
            img1: 'temperaturaHigh', //IMAGEM 1 
            img2: 'temperaturaNormal', //IMAGEM 2
            img3: 'temperaturaLow', //IMAGEM 3 
            par1: 30, //LIMITE SUPERIOR
            par2: 20 //LIMITE INFERIOR
        },
        {
            id: 'fumo',
            symbol: '',
            img1: 'fumoHigh',
            img2: 'fumoNormal',
            img3: null,
            par1: 'Fogo',
            par2: null
        }, {
            id: 'lotacao',
            symbol: '',
            img1: 'peopleHigh',
            img2: 'peopleNormal',
            img3: 'peopleLow',
            par1: 500,
            par2: 300
        }, {
            id: 'humidade',
            symbol: '%',
            img1: 'humidadeHigh',
            img2: 'humidadeNormal',
            img3: 'humidadeLow',
            par1: 70,
            par2: 30
        }, {
            id: 'arcondicionado',
            symbol: '',
            img1: 'acOn',
            img2: 'acOf',
            img3: null,
            par1: "Ligado",
            par2: null
        }, {
            id: 'sprinkler',
            symbol: '',
            img1: 'sprinklerOn',
            img2: 'sprinklerOf',
            img3: null,
            par1: 'Ligado',
            par2: null
        }, {
            id: 'estores',
            symbol: '',
            img1: 'estoresOn',
            img2: 'estoresOf',
            img3: null,
            par1: 'Ligado',
            par2: null
        }, {
            id: 'som',
            symbol: '',
            img1: 'somOn',
            img2: 'somOf',
            img3: null,
            par1: 'Ligado',
            par2: null
        }
    ];

    //FUNÇÃO ASSINCRONA PARA FAZER GET À API 
    async function getData(nome) {
        //Concatena o url da api preparado para o get + o nome passado por parâmetro
        const url = apiUrl + nome;
        try {
            const response = await fetch(url); // realiza um fetch
            if (response.ok) {
                const data = await response.json();
                return data; // retorna a resposta com nome, valor, data
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
    //FUNÇÃO TOGGLER DO SOM AMBIENTE
    async function togglerSom() {
        const value = await getValue('som'); //Vai buscar o valor atual do card do Som Ambiente
        const togglerSomCheckbox = document.getElementById('toggler-som'); //Cria um variável para controlar o toggler

        if (value == "Ligado") { //Se o valor é ligado
            togglerSomCheckbox.checked = true; //o toggler fica checked
        } else if (value == "Desligado") { // Se o valor é desligado 
            togglerSomCheckbox.checked = false; // o toggler fica unchecked
        }
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
        // Atualizar a imagem do upload
        updateUploadImage();
        //Atualizar a cada 1 segundo
        setTimeout(() => updateData(d), 1000);
    }

    //FUNCAO QUE ATUALIZA TODOS OS DISPOSITIVOS E O TOGGLER SOM
    async function startFetching() {
        for (const d of dispositivos) {
            await updateData(d);
        }
        await togglerSom();
        setTimeout(() => togglerSom(), 0);
    }
    //Quando o documento tiver sido carregado completamente
    document.addEventListener('DOMContentLoaded', async (event) => {
        // //Cria um variável para controlar o toggler
        const checkbox = document.getElementById('toggler-som');
        const checkboxMudanca = () => {
            if (checkbox.checked) { //Se estiver check significa que foi ligado
                postToAPI('som', 'Ligado');
            } else { //Se nao significa que foi desligado
                postToAPI('som', 'Desligado');
            }
        };

        checkbox.addEventListener('change', checkboxMudanca); //Evento de ser pressionado o botão toggler

        for (const d of
                dispositivos) { //Ao ser carregado o DOM queremos que apareça logo as informações por isso percorremos os dispositivos
            await fetchAndUpdate(d.id, d.symbol); //Atualiza o conteúdo 
            const value = await getValue(d.id); //Obtem o valor
            updateImage(d.id, value, d.img1, d.img2, d.img3, d.par1, d.par2); //Atualiza Imagens
            updateAviso(d.id, value, d.par1, d.par2); // Atualiza avisos
        }
        updateUploadImage(); //Atualiza imagem de upload
        startFetching(); // começa o fetching, que tem funcoes com ciclo de repeticao
    });

    // FUNÇÃO PARA ATUALIZAR IMAGEM DO UPLOAD 
    async function updateUploadImage() {
        // Obtem o elemento com o id da imagem 
        const imgElement = document.getElementById('uploadImage');
        // Obtem o elemento com o id da data
        const imgDateElement = document.getElementById('uploadImageDate');
        try {
            //Tenta obter a imagem mais recente
            const imageResponse = await fetch(apiUrlImg + '?imagemAtual');
            if (imageResponse.ok) {
                //Se conseguir cria um objeto blob com a resposta
                const blob = await imageResponse.blob();
                const url = URL.createObjectURL(blob); //Cria um objeto URL
                imgElement.src = url; // atualiza o elemento da imagem
            } else {
                console.error('Erro:', imageResponse.statusText); //Erro 
            }
            // Tenta obter a data da imagem mais recente
            const dataResponse = await fetch(apiUrlImg + '?imagemData');
            if (dataResponse.ok) {
                // Se conseguir atualiza o elemento da data com a data obtida 
                const data = await dataResponse.text();
                imgDateElement.innerText = data;
            } else {
                console.error('Erro:', dataResponse.statusText); // Erro
            }
        } catch (erro) {
            console.error('Erro:', erro); // Erro
        }
    }

    function toIsoString(date) { //Função para retornar a hora sem nenhum offset da zona
        var tzo = -date.getTimezoneOffset(),
            dif = tzo >= 0 ? '+' : '-',
            pad = function(num) {
                return (num < 10 ? '0' : '') + num;
            };

        return date.getFullYear() +
            '-' + pad(date.getMonth() + 1) +
            '-' + pad(date.getDate()) +
            ' ' + pad(date.getHours()) +
            ':' + pad(date.getMinutes()) +
            ':' + pad(date.getSeconds());
    }
    // Função para fazer POST para API
    function postToAPI(nome, valor) {
        // Vai buscar a data atual
        const agora = toIsoString(new Date());
        // Cria uma variavel para organizar o envio dos dados de acordo com o POST 
        const payload = {
            nome: nome,
            valor: valor,
            hora: agora
        };
        console.log(payload) // Imprime na consola
        fetch(apiUrl, { // Realiza um fetch com o método POST à API
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(payload).toString()
            })
            .then(response => response.text())
            .then(data => { // Imprime a resposta
                console.log(data);
            })
            .catch(erro => { // Imprime o erro
                console.error('Erro:', erro);
            });
    }
</script>

<!-- Sensores -->
<div class="row">
    <div class="meu_cartao col-md bg-info bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <!-- Trocar o nome (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_nome  -->
                <strong id="temperatura_nome"></strong>
            </div>
            <div class="imagem card-body">
                <!-- Trocar a imagem (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_img  -->
                <img id="temperatura_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <!-- Trocar o valor (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_valor  -->
                    <span class="value_cartao" id="temperatura_valor"></span>
                </div>
                <div class="info_cartao">
                    <!-- Trocar o valor (todos os dispositivos têm um elemento seguindo a nomenclatura {nome}_hora  -->
                    <span id="temperatura_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <!-- GET com nome do dispositivo para mostrar historico -->
                <a class="btn btn-primary" href="historico.php?nome=temperatura">Histórico</a>
            </div>
        </div>
    </div>

    <div class="meu_cartao col-md bg-info bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="fumo_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="fumo_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="fumo_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="fumo_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=fumo">Histórico</a>
            </div>
        </div>
    </div>

    <div class="meu_cartao col-md bg-info bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="lotacao_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="lotacao_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="lotacao_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="lotacao_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=lotacao">Histórico</a>
            </div>
        </div>
    </div>
    <div class="meu_cartao col-md bg-info bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="humidade_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="humidade_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="humidade_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="humidade_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=humidade">Histórico</a>
            </div>
        </div>
    </div>
</div>

<!-- Atuadores -->
<div class="row">
    <div class="meu_cartao col-md bg-danger bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="arcondicionado_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="arcondicionado_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="arcondicionado_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="arcondicionado_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=arcondicionado">Histórico</a>
            </div>
        </div>
    </div>

    <div class="meu_cartao col-md bg-danger bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="sprinkler_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="sprinkler_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="sprinkler_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="sprinkler_hora"></span>
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <a class="btn btn-primary" href="historico.php?nome=sprinkler">Histórico</a>
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
    <div class="meu_cartao col-md bg-danger bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong id="som_nome"></strong>
            </div>
            <div class="imagem card-body">
                <img id="som_img" src="" alt="">
            </div>
            <div class="card-footer">
                <div class="info_cartao">
                    <br>
                    <span id="som_valor" class="value_cartao"></span>
                </div>
                <div class="info_cartao">
                    <span id="som_hora"></span>
                    <br>
                </div>
            </div>
            <div class=" meu_cartao_tras">
                <div class="container">
                    <a class="btn btn-primary" href="historico.php?nome=som">Histórico</a>
                    <div class="toggler">
                        <input id="toggler-som" name="toggler-som" type="checkbox" value="1">
                        <label for="toggler-som">
                            <svg class="toggler-on" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5"></polyline>
                            </svg>
                            <svg class="toggler-off" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                                <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
                            </svg>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row justify-content-center">
    <div class="meu_cartao col-md bg-info bg-gradient">
        <div class="meu_cartao_inner card text-center">
            <div class="titulo_cartao card-header">
                <strong>Imagem</strong>
            </div>
            <div class="imagem card-body">
                <!-- elemento img com id uploadImage para ser atualizada -->
                <img id="uploadImage" style="width:100%">
            </div>
            <div class="card-footer">
                <!-- div com id uploadImageDate para ser atualizada com a data da imagem -->
                <div class="info_cartao" id="uploadImageDate">
                    <br>
                </div>
            </div>
            <div class="meu_cartao_tras">
                <!-- GET com nome do dispositivo para mostrar historico -->
                <a class="btn btn-primary" href="historicoImages.php">Histórico</a>
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
                <td id="temperatura_nome"></td>
                <td id="temperatura_valor"></td>
                <td id="temperatura_aviso"></td>
            </tr>
            <tr>
                <td id="fumo_nome"></td>
                <td id="fumo_valor"></td>
                <td id="fumo_aviso"></td>
            </tr>
            <tr>
                <td id="lotacao_nome"></td>
                <td id="lotacao_valor"></td>
                <td id="lotacao_aviso"></td>
            </tr>
            <tr>
                <td id="humidade_nome"></td>
                <td id="humidade_valor"></td>
                <td id="humidade_aviso"></td>
            </tr>
            <tr>
                <td id="arcondicionado_nome"></td>
                <td id="arcondicionado_valor"></td>
                <td id="arcondicionado_aviso"></td>
            </tr>
            <tr>
                <td id="sprinkler_nome"></td>
                <td id="sprinkler_valor"></td>
                <td id="sprinkler_aviso"></td>
            </tr>
            <tr>
                <td id="estores_nome"></td>
                <td id="estores_valor"></td>
                <td id="estores_aviso"></td>
            </tr>
            <tr>
                <td id="som_nome"></td>
                <td id="som_valor"></td>
                <td id="som_aviso"></td>
            </tr>
        </tbody>
    </table>
</div>