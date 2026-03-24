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
GPIO.setup(4, GPIO.OUT)  # Saída do vermelho
GPIO.setup(17, GPIO.OUT)  # Saída do verde
GPIO.setup(22, GPIO.OUT)  # Saída do Azul

hz = 75  # Frequência do PWM
# Iniciar o PWM nos pinos e definir como objetos de cores
red = GPIO.PWM(4, hz)
green = GPIO.PWM(17, hz)
blue = GPIO.PWM(22, hz)

# Duty cycle - para criar a cor laranja -> 100% vermelho e 50% verde e 0 % azul
reddc = 100
greendc = 50
bluedc = 0

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


# Ciclo Infinito até que o user prima CTRL + C
print("--- Prima CTRL + C para terminar ---")
while True:
    try:
        # Pergunta se pretende ligar ou desligar o Som Ambinente
        resposta = input(
            "Quer ligar ou desligar o som ambiente? (1-ligar 0-desligar): ")
        if resposta == '1':
            # Se for 1, faz um POST como Ligado  e acende LED laranja durante 5 segundos, indicativo que foi feita uma operação
            post2API("som", "Ligado")
            blue.start(bluedc)
            green.start(greendc)
            red.start(reddc)
            tm.sleep(5)
            blue.stop()
            green.stop()
            red.stop()
            # Se for 0, faz um POST como Desligado e acende LED laranja durante 5 segundos, indicativo que foi feita uma operação
        elif resposta == '0':
            post2API("som", "Desligado")
            blue.start(bluedc)
            green.start(greendc)
            red.start(reddc)
            tm.sleep(5)
            blue.stop()
            green.stop()
            red.stop()
        else:
            print("Tem de selecionar 1 ou 0")
    except KeyboardInterrupt:
        print('\nA aplicação foi interrompida pelo utilizador')
        break
    except Exception as e:  # Ocorrência de alguma Exception
        print('Erro:', e)
        print("Tenta outra vez")
    finally:  # Termina programa e limpa os pinos GPIO
        GPIO.cleanup()
        print('Terminou o programa')
