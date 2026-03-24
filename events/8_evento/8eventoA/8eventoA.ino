//Bibliotecas 
#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <NTPClient.h>
#include <WiFiUdp.h> // Pré-instalada com o Arduino IDE
#include <TimeLib.h>

#define FLAME_SENSOR_PIN 8 // Pino do Flame sensor

WiFiUDP clienteUDP;
//Servidor de NTP do IPLeiria: ntp.ipleiria.pt
//Fora do IPLeiria servidor: 0.pool.ntp.org
char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);

char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";

char URL[] = "iot.dei.estg.ipleiria.pt"; //URL do servidor 
int PORTO = 80; // ou outro porto que esteja definido no servidor

WiFiClient clienteWifi;// Objeto WiFiClient para realizar conexao Wifi
//Objeto HttpClient para realizar a conexao ao servidor HTTP
HttpClient clienteHTTP = HttpClient(clienteWifi, URL, PORTO);



String resposta = ""; //Resposta 

String URLPath = "/ti/g049/api/api.php"; //URL da API 
String contentType = "application/x-www-form-urlencoded"; //Tipo do envio

void setup() {
  Serial.begin(115200); // Iniciar a porta serial
  while(!Serial);
  pinMode(LED_BUILTIN, OUTPUT);
  pinMode(FLAME_SENSOR_PIN, INPUT); //Definir o pino do FLAME SENSOR como INPUT
  WiFi.begin(SSID, PASS_WIFI); //Começar a conexao
  while(WiFi.status() != WL_CONNECTED){
    Serial.print(".");
    delay(500);
  }
}

void loop() {
  char datahora[20]; //String para guardar a data
  update_time(datahora); //Atualizar a data

  int flameStatus = digitalRead(FLAME_SENSOR_PIN);  //Obter a leitura do FLAME SENSOR

  Serial.println("Status do Sensor de Fumo: " + String(flameStatus)); //Imprimir o valor lido
  if(flameStatus > 0){ // Se o status for  > 0 existe indícios de fogo
    post2API("fumo", "Fogo", datahora); // Faz POST do sensor como tendo sido detetado Fogo
  }
  else{ // Se não, a situação encontra-se normal
    post2API("fumo", "Normal", datahora); // Faz POST do sensor como Normal
  }
 delay(5000);  //Espera 5 segundos
}


 //Função para realizar um POST à API
void post2API(String nome, String valor, String hora) {
   //Cria o body de acordo com o POST aceite pela API, preenchido com os parâmetros de entrada
  String body = "nome=" + nome + "&valor=" + valor + "&hora=" + hora;
  //Realiza o POST para a API
  clienteHTTP.post(URLPath, contentType, body);
  while (clienteHTTP.connected()) {
    if (clienteHTTP.available()) { //Se conseguir comunicar 
      int responseStatusCode = clienteHTTP.responseStatusCode(); //Código de resposta
      String responseBody = clienteHTTP.responseBody(); //Corpo da Resposta
      //Imprime o Status Code e Resposta
      Serial.println("Status Code: " + String(responseStatusCode) + " Resposta: " + responseBody);
    }
  }
}

//Função para atualizar a data em tempo real
void update_time(char *datahora) {
  //Atualizar o cliente NTP
  clienteNTP.update();
  //Obter a data
  unsigned long epochTime = clienteNTP.getEpochTime();
  //Repartir a data e escrever na string de entrada da função
  sprintf(datahora, "%04d-%02d-%02d %02d:%02d:%02d", year(epochTime), month(epochTime), day(epochTime), hour(epochTime), minute(epochTime), second(epochTime));
}
