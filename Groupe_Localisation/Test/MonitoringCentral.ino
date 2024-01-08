#include <M5Stack.h>
#include <M5LoRa.h>
#include <SPI.h>

void setup()
{
  M5.begin();
  LoRa.setPins();  // Configure les broches LoRa (CS, reset, IRQ)
  M5.Lcd.setTextSize(2);  // Configure la taille du texte à 2
  M5.Lcd.println("Reception");
  M5.Lcd.setCursor(0, 25);
  M5.Lcd.println("Status: \nLieu:");
  pinMode(16, INPUT);
  LoRa.begin(868E6);
}

void loop() {
  M5.Lcd.fillRect(90, 25, 220, 100, BLACK); // Dessine un rectangle noir 220 par 100 à (90,25).
  
  if(digitalRead(16)==1)// SI ON DETECTE DU MOUVEMENT
  { 
      M5.Lcd.setCursor(95, 25);
      M5.Lcd.println("Mouvement detecte");
        
      if(LoRa.parsePacket())  // ET QU'ON NOUS DIT QUE L'EMETTEUR DETECTE DU MOUVEMENT
      { 
        String LoRaData = LoRa.readString();        
        if (LoRaData == "1")
        {
          M5.Lcd.setCursor(95,40);
          M5.Lcd.println("Sdb");  // ALORS LE GARS EST AU MILIEU - SALLE DE BAIN
        }
      }
      else
      {
        M5.Lcd.setCursor(95,40);  // SI PAS DE MESSAGE RECU, ON EST A LA DOUCHE
        M5.Lcd.println("Douche");
      }
    }
    else // SI ON DETECTE R
     {
        if(LoRa.parsePacket())  //MAIS QU'ON RECOIT UN MESSAGE 1 DE L'EMETTEUR, alors LE GARS EST AU LAVABOT
        {  
          M5.Lcd.setCursor(95,40);
          M5.Lcd.println("Lavabot");
        }
      }
    delay (5000);
}
