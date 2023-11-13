#include <SPI.h>
#include <LoRa.h>
#include <M5Stack.h>

int counter = 0;

void setup() {
  M5.begin();             // Init M5Stack.
  M5.Power.begin();       // Init power
  M5.Lcd.setTextSize(2);  // Set the text size to 2
  M5.Lcd.println("Detection");
  M5.Lcd.setCursor(0,25);  // Position the cursor at (0,25).
  M5.Lcd.println("Status: \nValue: ");
  pinMode(36, INPUT);  // Set pin 36 to input mode.
  Serial.begin(9600);
  
  while (!Serial);
  Serial.println("LoRa Sender");
  if (!LoRa.begin(868E6)) {
    Serial.println("Starting LoRa failed!");
    while (1);
  }
}

void loop() {
  M5.Lcd.fillRect(90, 25, 180, 50,BLACK);  // Draw a black rectangle 180 by 50 at (90,25).
    if (digitalRead(36) == 1) 
    {  // If pin 36 reads a value of 1. Movement detected
      M5.Lcd.setCursor(95, 25);
      M5.Lcd.print("MOUVEMENT !!!!");
      M5.Lcd.setCursor(95, 45);
      M5.Lcd.print("1");
      // send packet
      LoRa.beginPacket();
      LoRa.print("Mouvement");
      LoRa.print(counter);
      LoRa.endPacket();
      counter++;
      delay(5000);
    } 
else 
  {
    M5.Lcd.setCursor(95, 25);
    M5.Lcd.print("PAS DE MOUVEMENT !");
    M5.Lcd.setCursor(95, 45);
    M5.Lcd.print("0");
    LoRa.beginPacket();
    LoRa.print("hello ");
    LoRa.print(counter);
    LoRa.endPacket();
    counter++;
    delay(5000);
  }
}
