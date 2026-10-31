#include <Arduino.h>
#include <Wire.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include "secrets.h"

#define PIN_SDA 21
#define PIN_SCL 22
#define PIN_MQ135 35

// Poradie: cervena, zelena, modra
const int RGB[] = {27, 25, 26};

Adafruit_BME280 bme;

bool bmeAvailable = false;

constexpr uint32_t SEND_INTERVAL_MS = 30000;
uint32_t lastSend = 0;

void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, pass);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

  for (int i = 0; i < 3; i++) {
    pinMode(RGB[i], OUTPUT);
    analogWrite(RGB[i], 0);
  }

  // Kratky test jednotlivych farieb pri starte
  analogWrite(RGB[0], 200);
  delay(500);
  analogWrite(RGB[1], 200);
  delay(500);
  analogWrite(RGB[2], 200);
  delay(500);
  for (int i = 0; i < 3; i++) 
    analogWrite(RGB[i], 0);

  Wire.begin(PIN_SDA, PIN_SCL);

  Serial.println();
  Serial.println("=== IoT Monitor: test senzorov ===");

  if (bme.begin(0x76)) {
    bmeAvailable = true;
    Serial.println("BME280 najdeny na adrese 0x76.");
  } else if (bme.begin(0x77)) {
    bmeAvailable = true;
    Serial.println("BME280 najdeny na adrese 0x77.");
  } else {
    Serial.println("CHYBA: BME280 sa nenasiel!");
  }

  Serial.println("Inicializacia dokoncena.");
  Serial.println();
}

void loop() {

  if (millis() - lastSend < SEND_INTERVAL_MS) return;
  lastSend = millis();

    
  float temp = bme.readTemperature();
  float humid = bme.readHumidity();
  float press = bme.readPressure();
  float ppm = 600;

  if (!isfinite(ppm) || ppm < 0 || ppm > 5000) { return; }

  if (isnan(temp) || isnan(humid) || isnan(press)) { 
    Serial.println("BME280: bez dostupnych udajov");
    return; 
  }

  if(WiFi.status()== WL_CONNECTED){
    WiFiClientSecure client;
    client.setInsecure(); 
    HTTPClient https;
    https.begin(client, url);
    https.setTimeout(5000);
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    String httpRequestData = String("api_key=") + api + "&temp=" + String(bme.readTemperature())
                          + "&humid=" + String(bme.readHumidity()) + "&press=" + String(bme.readPressure()/100.0F) 
                          + "&ppm=600";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    int httpResponseCode = https.POST(httpRequestData);
    
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    https.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }

  Serial.println("----- Namerane hodnoty -----");

  if (bmeAvailable) {
    Serial.print("Teplota: ");
    Serial.print(temp);
    Serial.println(" C");

    Serial.print("Vlhkost: ");
    Serial.print(humid);
    Serial.println(" %");

    Serial.print("Atmosfericky tlak: ");
    Serial.print(press / 100.0F, 2);
    Serial.println(" hPa");
  }

/*  if (mq135raw < 1000) {
    analogWrite(RGB[1], 200); 
    Serial.println("RGB: ZELENA");
  } else if (mq135raw < 1200) {
    analogWrite(RGB[0], 200);   
    Serial.println("RGB: ZLTA");
  } else {
    analogWrite(RGB[1], 0);  
    Serial.println("RGB: CERVENA");
  }
*/

}
