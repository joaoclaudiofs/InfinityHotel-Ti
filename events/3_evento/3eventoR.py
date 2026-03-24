# Bibliotecas
import cv2
import requests as rq

# URL da fotografia e da API Upload
webcam_url = "https://rooftop.tryfail.net/image.jpeg"
url = 'https://iot.dei.estg.ipleiria.pt/ti/g049/api/upload.php'

# Inicia uma captura ao URL
cap = cv2.VideoCapture(webcam_url)
# Guarda uma frame e o ret indica se foi feito com sucesso
ret, frame = cap.read()

if ret:  # Se correr bem
    image_path = 'captura.jpg'  # Criamos um caminho para a imagem
    cv2.imwrite(image_path, frame)  # Escrevemos a imagem no caminho
    with open(image_path, 'rb') as f:  # Abrimos a imagem acaba de escrever
        # Dizemos que o ficheiro para enviar no POST é a imagem jpeg acabada de abrir
        files = {'imagem': (image_path, f, 'image/jpeg')}

        r = rq.post(url, files=files)  # Realizamos o POST

        if r.status_code != 200:  # Se correr mal
            print("Dados do post errados")
        else:  # Se correr bem
            print("Upload da última imagem feito com sucesso!")

else:  # Falhou
    print("Falha ao capturar a imagem")
cap.release()  # Limpar o objeto associado a cap
