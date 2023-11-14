#include <M5Stack.h>
#include <M5LoRa.h>
#include <SPI.h>


void setup() {
    M5.begin();
    // Override the default CS, reset, and IRQ pins (optional)
    LoRa.setPins();  // Default set CS, reset, IRQ pin
    M5.Lcd.setTextSize(2); // Set the text size to 2
    M5.Lcd.println("Detection");
    M5.Lcd.setCursor(0, 25); // Postion of the cursor at (0,25)
    M5.Lcd.println("Status: \nValue: \nEmission :");
    pinMode(36, INPUT);

    // Frequency in Hz (433E6, 866E6, 915E6)
    LoRa.begin(866E6);
}

void loop() {
    M5.Lcd.fillRect(90, 25, 180, 75, BLACK);  // Draw a black rectangle 180 by 50 at (90,25).
    static uint32_t counter;
    // Detecting something
    if (digitalRead(36) == 1) 
    {
      M5.Lcd.setCursor(95, 25);
      M5.Lcd.println("Mouvement");
      M5.Lcd.setCursor(95, 50);
      M5.Lcd.println("1");
      M5.Lcd.setCursor(95, 70);
      M5.Lcd.println("J'envoie R");
          // Sending packets
      LoRa.beginPacket();
      LoRa.print("Mouvement");
      LoRa.endPacket();
    }
    else 
    {
      M5.Lcd.setCursor(95, 25);
      M5.Lcd.println("Pas de mouvement");
      M5.Lcd.setCursor(95, 50);
      M5.Lcd.println("0");
      M5.Lcd.setCursor(95, 70);
      M5.Lcd.println("J'envoie R");
    }
    // Add a short delay to avoid reading too frequently
    delay(1000);
}
