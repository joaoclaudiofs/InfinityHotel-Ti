# Bibliotecas
import bluepy.btle as bluepy
import RPi.GPIO as GPIO
import time as tm
import requests as rq
import datetime as dt

# Tirar warnings
GPIO.setwarnings(False)
# Configurar a biblioteca GPIO
GPIO.setmode(GPIO.BCM)

# Configuração dos pinos GPIO como saídas
GPIO.setup(4, GPIO.OUT)  # Saída do vermelho
GPIO.setup(17, GPIO.OUT)  # Saída do verde
GPIO.setup(22, GPIO.OUT)   # Saída do Azul

hz = 75  # Frequência do PWM
# Iniciar o PWM nos pinos e definir o objeto da cor azul
blue = GPIO.PWM(22, hz)
# Duty cycle - para criar a cor azul -> 100% azul
bludc = 100

# Função para fazer POST para API


def post2API(nome, valor):
    # Vai buscar a data atual
    agora = dt.datetime.now()
    # Imprime a data
    print(agora.strftime("%Y-%m-%d %H:%M:%S"))
    # Payload para juntar os parâmetros necessários para fazer corretamente o POST
    payload = {'nome': nome, 'valor': valor,
               'hora': agora.strftime("%Y-%m-%d %H:%M:%S")}
    # Envio dos dados(data) para a API
    r = rq.post(
        'http://iot.dei.estg.ipleiria.pt/ti/g049/api/api.php', data=payload)
    if r.status_code == rq.codes.ok:
        print(r.text)  # Imprime a resposta de o código for ok (200)
    else:
        print(r.raise_for_status)  # Imprime o HTTPError


# Função para fazer GET da API


def getFromApi(nome):
    try:
        # Faz um GET para API de acordo com o nome passado no parâmetro
        r = rq.get(
            f'http://iot.dei.estg.ipleiria.pt/ti/g049/api/api.php?nome={nome}')
        r.raise_for_status()  # Verifica erros
        # Se o tipo de conteúdo for json a resposta será em formato JSON, se não será em formato text
        if r.headers['Content-Type'] == 'application/json':
            data = r.json()
        else:
            data = r.text
        return data.strip()
    except rq.exceptions.HTTPError as error:  # ERRO de HTTP
        print(f"Aconteceu um erro de HTTP: {error}")
        return None
    except Exception as error:  # Outra Exceção
        print(f"Aconteceu um erro: {error}")
        return None

# Criar a classe Procura que herda de bluepy.DefaultDelegate


class Procura(bluepy.DefaultDelegate):

    # Método executado ao descobrir um dispositivo bluetooth
    def handleDiscovery(self, dev, isNewDev, isNewData):
        # Verifica se o dispositivo encontrado tem um RSSI maior que -35 e tem o endereço específico
        if dev.rssi > -35 and dev.addr == "d0:e5:1b:da:90:ff":
            # Imprime mensagem a indicar que alguém entrou no hotel com a sua pulseira
            print("Uma pessoa acabou de entrar no hotel: ",
                  dev.addr, " RSSI:", dev.rssi)
            print("A ligar o led")
            # Liga o LED AZUL
            blue.start(bludc)
            incrementaLotacao()  # Incrementa a Lotação
            tm.sleep(1)  # Espera 1 segundo
            print("A desligar o led")
            blue.stop()  # Desliga o LED
            tm.sleep(1)  # Espera 1 segundo

# Método para incrementar lotação


def incrementaLotacao():
    # Obtem a partir de um GET à API o valor da lotação
    lotacao = int(getFromApi("lotacao"))
    novaLotacao = (lotacao + 1)  # Incrementa 1 a esse valor
    post2API("lotacao", novaLotacao)  # E faz um POST do novo valor


# Cria um objeto scanner Bluetooth e associa-lhe a classe Procura
scanner = bluepy.Scanner().withDelegate(Procura())
# Procura em modo passivo por dispositivos Bluetooth durante 30 segundos
devices = scanner.scan(30, passive=True)
