//Bibliotecas 
#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <TimeLib.h>

#define BUZZER_PIN 6 // Pino digital onde está ligado o buzzer

WiFiUDP clienteUDP;
// Servidor de NTP do IPLeiria: ntp.ipleiria.pt
// Fora do IPLeiria servidor: 0.pool.ntp.org
char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);

char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";

char URL[] = "iot.dei.estg.ipleiria.pt"; //URL do servidor 
int PORTO = 80; // ou outro porto que esteja definido no servidor

WiFiClient clienteWifi;   // Objeto WiFiClient para realizar conexao Wifi
//Objeto HttpClient para realizar a conexao ao servidor HTTP
HttpClient clienteHTTP = HttpClient(clienteWifi, URL, PORTO); 



String URLPath = "/ti/g049/api/api.php";  //URL da API 
String enviaNome = "som"; //Dispositivo 

void setup() {
  pinMode(BUZZER_PIN, OUTPUT); //Colocar o PIN do Buzzer como OUTPUT
  Serial.begin(115200); // Iniciar a porta serial
  while(!Serial);
  pinMode(LED_BUILTIN, OUTPUT);
  WiFi.begin(SSID, PASS_WIFI);  //Começar a conexao
  while(WiFi.status() != WL_CONNECTED){
    Serial.print(".");
    delay(500);
  }
  // Serial.println((IPAddress)WiFi.localIP());
  // Serial.println((IPAddress)WiFi.subnetMask());
  // Serial.println((IPAddress)WiFi.gatewayIP());
  // Serial.println(WiFi.RSSI());
}

void loop() {
  // Faz GET à API para obter o estado do Som ambiente
  String somStatus = getFromAPI(enviaNome);

  //Se o Get retornar que o Som Ambiente está "Ligado"
  if (somStatus == "Ligado") {
      tone(BUZZER_PIN, 500); //O Buzzer vai tocar na frequência 500
      Serial.println("Buzzer Ligado!");
  } else if (somStatus == "Desligado") {
      noTone(BUZZER_PIN); //Se não, fica desligado
      Serial.println("Buzzer Desligado!");
  }

  delay(5000); //Espera 5 segundos
}

//Função para realizar um GET à API
String getFromAPI(String nome) {
  String query = URLPath + "?nome=" + nome; // Faz a query de acordo com o dispositivo passado por parâmetro
  clienteHTTP.get(query);  //Faz um GET
  while (clienteHTTP.connected()) {
    if (clienteHTTP.available()) {
      int responseStatusCode = clienteHTTP.responseStatusCode(); //Código da resposta 
      String responseBody = clienteHTTP.responseBody(); //Corpo da resposta
      Serial.println("Status Code: " + String(responseStatusCode) + " Resposta: " + responseBody); //Print do código e corpo da resposta
      return responseBody; //Retorna o Corpo da resposta
    }
  }
  return "";
}
