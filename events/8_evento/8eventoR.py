# Bibliotecas
import requests as rq
import time as tm
import RPi.GPIO as GPIO
import datetime as dt

# Tirar warnings
GPIO.setwarnings(False)
# Configurar a biblioteca GPIO
GPIO.setmode(GPIO.BCM)

# Configuração dos pinos GPIO como saídas
GPIO.setup(4, GPIO.OUT)   # Saída do vermelho
GPIO.setup(17, GPIO.OUT)  # Saída do verde
GPIO.setup(22, GPIO.OUT)  # Saída do Azul
hz = 75  # Frequência do PWM
# Iniciar o PWM nos pinos e definir o objeto da cor vermelha
red = GPIO.PWM(4, hz)
# Duty cycle - para criar a cor vermelha -> 100% vermelha
reddc = 100

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


# Ciclo Infinito até que o user prima CTRL + C
print("--- Prima CTRL + C para terminar ---")
try:
    while True:
        # Faz um GET à API do status do fumo
        fumo_status = getFromApi('fumo')
        if fumo_status:
            # Se for bem sucedido imprime o status
            print(f"Status do sensor de fumo: {fumo_status}")
            if fumo_status == "Fogo":  # Se estiver a ocorrer algum fogo, faz POST do sprinkler como Ligado
                post2API("sprinkler", "Ligado")
            else:
                # Se não estiver a ocorrer nenhum fogo, faz POST do sprinkler como Desligado
                post2API("sprinkler", "Desligado")
        # Faz um GET à API para obter o status do Sprinkler
        sprinker_status = getFromApi('sprinkler')
        if sprinker_status:  # Se for bem sucedido
            # Imprime o resultado com o cuidado de retirar os espaços
            print(sprinker_status.strip())
            # Se estiver Ligado
            if sprinker_status.strip() == "Ligado":
                red.start(reddc)  # Liga o LED vermelho
            else:
                red.stop()  # Se não, desliga o LED vermelho
        tm.sleep(5)   # Espera 5 segundos
except KeyboardInterrupt:
    print('\nA aplicação foi interrompida pelo utilizador')
except Exception as e:  # Ocorrência de alguma Exception
    print('Erro:', e)
    print("A tentar outra vez...")

finally:
    # Termina programa, desliga a cor vermelho e limpa os pinos GPIO
    red.stop()
    GPIO.cleanup()
    print('Terminou o programa')
