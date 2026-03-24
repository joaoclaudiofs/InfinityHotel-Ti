//Bibliotecas 
#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <DHT.h>
#include <NTPClient.h>
#include <WiFiUdp.h> 
#include <TimeLib.h>
 
#define DHTPIN 0 // Pino Digital onde está ligado o sensor
#define DHTTYPE DHT11 // Tipo de sensor DHT
 
DHT dht(DHTPIN, DHTTYPE); // Instanciar e declarar a class DHT
 
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
 

 
//Declaração das variáveis a serem utilizadas
String resposta = ""; //Resposta 
float temp; //Temperatura que será devolvida pelo sensor
String URLPath = "/ti/g049/api/api.php"; //URL da API 
String contentType = "application/x-www-form-urlencoded";  //Tipo do envio
String enviaNome = "temperatura";//Dispositivos para enviar p/API
String enviaNome2 = "humidade";
String enviaValor = "-2"; //Valor para enviar
String enviaHora = "2024-05-03 12:32:00"; //Hora para enviar
 
void setup() {
 
  Serial.begin(115200); // Iniciar a porta serial
  while(!Serial);
  pinMode(LED_BUILTIN, OUTPUT);
  WiFi.begin(SSID, PASS_WIFI);  //Começar a conexao
  while(WiFi.status() != WL_CONNECTED){
        Serial.println(".");
        delay(500);
  }
  // Serial.println((IPAddress)WiFi.localIP());
  // Serial.println((IPAddress)WiFi.subnetMask());
  // Serial.println((IPAddress)WiFi.gatewayIP());
  // Serial.println( WiFi.RSSI());
  dht.begin();
}
 
void loop() {
  char datahora[20]; //String para guardar a data
  update_time(datahora); //Atualizar a data
  Serial.println(dht.readTemperature()); //Imprimir a Temperatura lida
  Serial.println(dht.readHumidity()); //Imprimir a Humidade lida
  enviaValor = String(dht.readTemperature()); //Guardar valor da temperatura
  post2API(enviaNome, enviaValor, datahora); //Fazer POST do valor da temperatura
  enviaValor = String(dht.readHumidity()); //Guardar valor da humidade
  post2API(enviaNome2, enviaValor, datahora); //Fazer POST do valor da temperatura
  delay(5000); //Espera 5 segundos
}
 
 //Função para realizar um POST à API
void post2API(String nome, String valor, String hora) {
  //Cria o body de acordo com o POST aceite pela API, preenchido com os parâmetros de entrada
  String body = "nome="+nome+"&valor="+valor+"&hora="+hora;
  //Realiza o POST para a API
  clienteHTTP.post(URLPath, contentType, body);
  while(clienteHTTP.connected()){
    if (clienteHTTP.available()){ //Se conseguir comunicar 
      int responseStatusCode = clienteHTTP.responseStatusCode(); //Código de resposta
      String responseBody = clienteHTTP.responseBody(); //Corpo da Resposta
      Serial.println("Status Code: "+String(responseStatusCode)+" Resposta: "+responseBody); //Imprime o Status Code e Resposta
    }
  }
}

//Função para atualizar a data em tempo real
void update_time(char *datahora){
  //Atualizar o cliente NTP
  clienteNTP.update();
  //Obter a data
  unsigned long epochTime = clienteNTP.getEpochTime();
  //Repartir a data e escrever na string de entrada da função
  sprintf(datahora, "%02d-%02d-%02d %02d:%02d:%02d", year(epochTime), month(epochTime), day(epochTime), hour(epochTime), minute(epochTime), second(epochTime));
}