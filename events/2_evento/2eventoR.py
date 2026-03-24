# Bibliotecas
import requests as rq
import time as tm
import datetime as dt


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
        print(r.text)    # Imprime a resposta de o código for ok (200)
    else:
        print(r.raise_for_status())  # Imprime o HTTPError


# Criar 2 dicionários, para atuadores e sensores
atuadores = {
    '0': 'arcondicionado',
    '1': 'sprinkler',
    '2': 'estores',
    '3': 'som'
}

sensores = {
    '4': 'temperatura',
    '5': 'fumo',
    '6': 'lotacao',
    '7': 'humidade'
}


# Ciclo Infinito até que o user prima CTRL + C
print("--- Prima CTRL + C para terminar ---")
try:
    while True:
        # Lista todos os atuadores
        print("|Atuador|:")
        for codigo, nome in atuadores.items():
            print(f"{codigo} - {nome}")
        # Lista todos os sensores
        print("|Sensor|:")
        for codigo, nome in sensores.items():
            print(f"{codigo} - {nome}")
        # Pergunta ao user qual dispositivo quer escolher
        # Strip para tirar espaços em branco acidentais
        codigoDispositivo = input("\n|Número do atuador ou sensor|: ").strip()
        # Se o código pertencer a um dos atuadores
        if codigoDispositivo in atuadores:
            atuador = atuadores[codigoDispositivo]
            input = input(
                # Pergunta de pretende desligar/ligar o atuador
                f"{atuador}\n0-Desligar\n1-Ligar\nCTRL+C para terminar: ")
            # Faz post de acordo com o input
            if input == '1':
                post2API(atuador, "Ligado")
            elif input == '0':
                post2API(atuador, "Desligado")
            else:
                print("Entrada inválida\nSó aceito 0 ou 1")
        # Se o código pertencer a um dos sensores
        elif codigoDispositivo in sensores:
            sensor = sensores[codigoDispositivo]
            # Pergunta que valor quer enviar para o sensor
            # Strip para tirar espaços em branco acidentais
            valor = input(f"Valor para o sensor {sensor}: ").strip()
            # Faz post de acordo com o input
            post2API(sensor, valor)
        # Se o código não pertencer a nenhum deles
        else:
            print("Dispositivo inválido\nTente novamente")
        tm.sleep(5)
except KeyboardInterrupt:
    print('\nA aplicação foi interrompida pelo utilizador')
except Exception as e:
    print('Erro inesperado:', e)  # Ocorrência de alguma Exception
    print("Tenta outra vez")
finally:  # Termina programa
    print('Terminou o programa')
