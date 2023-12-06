#include <stdio.h>

#include <M5Stack.h>
#include <M5LoRa.h>
#include <lmic.h> //Library allowing an object to connect to a LoRaWAN network.
#include <hal/hal.h> //Allows to run LMIC on top of the Arduino environment
#include <SPI.h> //Allows to manage the exchange of information between the Arduino board and the sensors

#include <WiFi.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
                                                                                                                          
//   Name:M5Stack00-3 (RT)  :  0C 7E 45 01 02 03 03 44
static const u1_t PROGMEM APPEUI[8]={ 0x0C, 0x7E, 0x45, 0x01, 0x02, 0x03, 0x03, 0x44  }; // 8 octets  
                                                                                                                          
void os_getArtEui (u1_t* buf) { memcpy_P(buf, APPEUI, 8);}

// This should also be in little endian format, see above.

static const u1_t PROGMEM DEVEUI[8]={ 0x44, 0x03, 0x03, 0x02, 0x01, 0x45, 0x7E, 0x0C }; 


void os_getDevEui (u1_t* buf) { memcpy_P(buf, DEVEUI, 8);}

//APPKEY DE M5Stack Ligne 69 : 01 02 03 04 05 06 07 08 09 0A 0B 0C 0D 0E 03 44
static const u1_t PROGMEM APPKEY[16] = { 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0A, 0x0B, 0x0C, 0x0D, 0x0E, 0x03, 0x44 }; //16 octets

void os_getDevKey (u1_t* buf) {  memcpy_P(buf, APPKEY, 16);}

static uint8_t mydata[] = "init";
static osjob_t sendjob;
const unsigned TX_INTERVAL = 10;
int presence;
int8_t getBatteryLevel();

//WiFi
const char *ssid = "ElBigotes"; // SSID of the AP at the MI
const char *password = "TucubanitoRico"; // PWD of the AP at the MI
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org"); //Connecting to a NTP server

// Pin mapping pour M5Stack (Thierry VAL)
const lmic_pinmap lmic_pins = {
    .nss = 5,
    .rxtx = LMIC_UNUSED_PIN,
    .rst = 26,
    .dio = {36, 35, 36},  //dio0 is connected to pin 36 of esp32, dio1: pin 35, dio2: aucune

    // mosi:pin 23, miso:pin 19, nss:pin 5, sck: pin 18, rst: pin 26
};

void printHex2(unsigned v)
{
  v &= 0xff;
  if (v < 16)
    {
      Serial.print('0');
      M5.Lcd.print("0");
    }  
    Serial.print(v, HEX);
    M5.Lcd.print(v);
}

void onEvent (ev_t ev)
{
    Serial.print(os_getTime());
    M5.Lcd.print(os_getTime());
    Serial.print(": ");
    M5.Lcd.print(": ");
    switch(ev) {
        case EV_SCAN_TIMEOUT:
            Serial.println(F("EV_SCAN_TIMEOUT"));
            M5.Lcd.print("EV_SCAN_TIMEOUT ");
            break;
        case EV_BEACON_FOUND:
            Serial.println(F("EV_BEACON_FOUND"));
            M5.Lcd.print("EV_BEACON_FOUND ");
            break;
        case EV_BEACON_MISSED:
            Serial.println(F("EV_BEACON_MISSED"));
            M5.Lcd.print("EV_BEACON_MISSED ");          
            break;
        case EV_BEACON_TRACKED:
            Serial.println(F("EV_BEACON_TRACKED"));
            M5.Lcd.print("EV_BEACON_TRACKED ");
            break;
        case EV_JOINING:
            Serial.println(F("EV_JOINING"));
            M5.Lcd.print("EV_JOINING ");
            break;
        case EV_JOINED:
            Serial.println(F("EV_JOINED"));
            M5.Lcd.print("EV_JOINED ");
            {
              u4_t netid = 0;
              devaddr_t devaddr = 0;
              u1_t nwkKey[16];
              u1_t artKey[16];
              LMIC_getSessionKeys(&netid, &devaddr, nwkKey, artKey);
              Serial.print("netid: ");
              M5.Lcd.print("netid: ");
              Serial.println(netid, DEC);
              M5.Lcd.print(netid);
              M5.Lcd.print(" ");
              Serial.print("devaddr: ");
              M5.Lcd.print("devaddr: ");
              Serial.println(devaddr, HEX);
              M5.Lcd.print(devaddr);
              M5.Lcd.print(" ");
              Serial.print("AppSKey: ");
              M5.Lcd.print("AppSKey: ");
              for (size_t i=0; i<sizeof(artKey); ++i) {
                if (i != 0)
                {
                  Serial.print("-");
                  M5.Lcd.print("-");
                }
                printHex2(artKey[i]);
              }
              Serial.println("");
              M5.Lcd.print(" NwkSKey: ");
              Serial.print("NwkSKey: ");
              for (size_t i=0; i<sizeof(nwkKey); ++i) {
                      if (i != 0)
                        {
                              Serial.print("-");
                              M5.Lcd.print("-");
                        }                    
                      printHex2(nwkKey[i]);
              }
              Serial.println();
              M5.Lcd.print(" ");
            }
            // Disable link check validation (automatically enabled
            // during join, but because slow data rates change max TX
	          // size, we don't use it in this example.
            LMIC_setLinkCheckMode(0);
            break;
        case EV_JOIN_FAILED:
            Serial.println(F("EV_JOIN_FAILED"));
            M5.Lcd.print("EV_JOIN_FAILED ");
            break;
        case EV_REJOIN_FAILED:
            Serial.println(F("EV_REJOIN_FAILED"));
            M5.Lcd.print("EV_REJOIN_FAILED ");
            break;
        case EV_TXCOMPLETE:
            Serial.println(F("EV_TXCOMPLETE (includes waiting for RX windows)"));
            M5.Lcd.print("EV_TXCOMPLETE (includes waiting for RX windows) ");
            if (LMIC.txrxFlags & TXRX_ACK)
              Serial.println(F("Received ack"));
              M5.Lcd.print("Received ack ");
            if (LMIC.dataLen) {
              Serial.print(F("Received "));
              M5.Lcd.print("Received ");
              Serial.print(LMIC.dataLen);
              M5.Lcd.print(LMIC.dataLen);
              Serial.println(F(" bytes of payload"));
              M5.Lcd.print(" bytes of payload ");
            }
            // Schedule next transmission
            os_setTimedCallback(&sendjob, os_getTime()+sec2osticks(TX_INTERVAL), do_send);
            break;
        case EV_LOST_TSYNC:
            Serial.println(F("EV_LOST_TSYNC"));
            M5.Lcd.print("EV_LOST_TSYNC ");
            break;
        case EV_RESET:
            Serial.println(F("EV_RESET"));
            M5.Lcd.print("EV_RESET ");
            break;
        case EV_RXCOMPLETE:
            // data received in ping slot
            Serial.println(F("EV_RXCOMPLETE "));
            M5.Lcd.print("EV_RXCOMPLETE ");
            break;
        case EV_LINK_DEAD:
            Serial.println(F("EV_LINK_DEAD"));
            M5.Lcd.print("EV_LINK_DEAD ");
            break;
        case EV_LINK_ALIVE:
            Serial.println(F("EV_LINK_ALIVE"));
            M5.Lcd.print("EV_LINK_ALIVE ");
            break;
        case EV_TXSTART:
            Serial.println(F("EV_TXSTART"));
            M5.Lcd.print("EV_TXSTART ");
            break;
        case EV_TXCANCELED:
            Serial.println(F("EV_TXCANCELED"));
            M5.Lcd.print("EV_TXCANCELED ");
            break;
        case EV_RXSTART:
            //do not print anything -- it wrecks timing 
            break;
        case EV_JOIN_TXCOMPLETE:
            Serial.println(F("EV_JOIN_TXCOMPLETE: no JoinAccept"));
            M5.Lcd.print("EV_JOIN_TXCOMPLETE: no JoinAccept ");
            break;
        default:
            Serial.print(F("Unknown event: "));
            M5.Lcd.print("Unknown event: ");
            Serial.println((unsigned) ev);
            M5.Lcd.print((unsigned) ev);
            M5.Lcd.print(" ");
            break;
    }
}

void do_send(osjob_t* j)
{
  static uint32_t sqn = 0;
  // Check if there is not a current TX/RX job running
   if (LMIC.opmode & OP_TXRXPEND) 
  {
      Serial.println(F("OP_TXRXPEND, not sending"));
      M5.Lcd.print("OP_TXRXPEND, not sending ");
  } 
  else 
  {
    M5.Lcd.fillRect(0, 0, 1000, 1000, BLACK); // Draws a black square 1000 pixel long.
    M5.Lcd.setCursor(0,0); //Set the cursor TOP LEFT
    String formattedDate = timeClient.getFormattedTime(); //extract date and time from the NTP server
    M5.Lcd.println(formattedDate);
    
    if (digitalRead(16)==1) //if mouvement detected
    {
      M5.Lcd.println("Presence Detected");
      presence=1;
      sprintf((char*)mydata,"{\"Presence\":%d, \"Battery\":%i}", presence, M5.Power.getBatteryLevel()); //JSON format
    }
    else
    {
      M5.Lcd.println("No presence Detected");
      presence=0;
      sprintf((char*)mydata,"{\"Presence\":%d, \"Battery\":%i}", presence, M5.Power.getBatteryLevel()); //JSON format
    }

    LMIC_setTxData2(1, mydata, strlen((char*)mydata), 0);
    sqn++;
    M5.Lcd.printf("Le message numero : %d a ete envoye \n", sqn);
  }
}  // Next TX is scheduled after TX_COMPLETE event.

void setup() 
{
  M5.begin(); 
  M5.Power.begin();
  M5.Lcd.begin();

  M5.Lcd.setTextSize(2);
  M5.Lcd.println("Test LoRaWAN LMIC ");

  while (! Serial);
    Serial.begin(115200);

  M5.Lcd.print("Starting ");
  WiFi.begin(ssid, password); // Connexion to the WiFi network

  while (WiFi.status() != WL_CONNECTED)
  { //If not connected to WiFi
    delay(1000);
    M5.Lcd.println("Connecting to WiFi...");
  }
  M5.Lcd.println("Connected to Wifi");
  M5.Lcd.println("Starting... ");

  // SYNCHRONISATION
  timeClient.begin();
  timeClient.update(); // Synchronization of the clock using a NTP server
  M5.Lcd.println("Clock settings synchronized");
  pinMode(16, INPUT); // Set the PINs of the PIR captor
  M5.Lcd.println("Waiting for sync...");

  int minute = timeClient.getMinutes(); //Get the minute
  while (timeClient.getMinutes() == minute) //Wait Until the minute has passed
  {
    delay (1000);
  }

  #ifdef VCC_ENABLE // For Pinoccio Scout boards
  pinMode(VCC_ENABLE, OUTPUT);
  digitalWrite(VCC_ENABLE, HIGH);
  delay(1000);
  #endif

  os_init(); // LMIC init
  LMIC_reset(); // Reset the MAC state. Session and pending data transfers will be discarded.
  do_send(&sendjob); // Start job (sending automatically starts OTAA too)
}

void loop() 
{
  os_runloop_once();
}