#define binome 3 // mettre ici votre numero de binome ou etudiant  

#include <lmic.h>
#include <hal/hal.h>
#include <SPI.h>

#include <M5Stack.h>

#include <TinyGPS++.h>
static const uint32_t GPSBaud = 9600;
// The TinyGPS++ object
TinyGPSPlus gps;

// The serial connection to the GPS device
HardwareSerial ss(2);


#define DEFAULT_LAT 43.6 // pour init

#define DEFAULT_LON 1.4

float flat = DEFAULT_LAT;
float flon = DEFAULT_LON;
unsigned long age;
int t;
unsigned long st;

static const u1_t PROGMEM APPEUI[8]={ 0x0C, 0x7E, 0x45, 0x01, 0x02, 0x03, 0x03, 0x03  };         // 8 octets  
                                                                                                                          
void os_getArtEui (u1_t* buf) { memcpy_P(buf, APPEUI, 8);}

static const u1_t PROGMEM DEVEUI[8]={ 0x03, 0x03, 0x03, 0x02, 0x01, 0x45, 0x7E, 0x0C }; 


void os_getDevEui (u1_t* buf) { memcpy_P(buf, DEVEUI, 8);}

static const u1_t PROGMEM APPKEY[16] = { 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0A, 0x0B, 0x0C, 0x0D, 0x0E, 0x03, 0x03 }; //16 octets

void os_getDevKey (u1_t* buf) {  memcpy_P(buf, APPKEY, 16);}

static uint8_t mydata[] = "init";

static osjob_t sendjob;

const unsigned TX_INTERVAL = 10;

const lmic_pinmap lmic_pins = {
    .nss = 5,
    .rxtx = LMIC_UNUSED_PIN,
    .rst = 26,
    .dio = {36, 35, 36},  //dio0 is connected to pin 36 of esp32, dio1: pin 35, dio2: aucune

    // mosi:pin 23, miso:pin 19, nss:pin 5, sck: pin 18, rst: pin 26
};

void printHex2(unsigned v) {
    v &= 0xff;
    if (v < 16)
      {
        Serial.print('0');
        M5.Lcd.print("0");
      }  
    Serial.print(v, HEX);
    M5.Lcd.print(v);
}

void do_send(osjob_t* j){

    static uint32_t sqn = 0;
    String flat_s;
    String flon_s;
    flat_s = String(flat,7);
    flon_s = String(flon,7);
  
    // Check if there is not a current TX/RX job running
    if (LMIC.opmode & OP_TXRXPEND) {
        Serial.println(F("OP_TXRXPEND, not sending"));
        M5.Lcd.print("OP_TXRXPEND, not sending ");
    } else {
        // Prepare upstream data transmission at the next possible time.

        sprintf((char*)mydata,"{\"etud\":%d,\"lat\":%s,\"lon\":%s}", binome, flat_s.c_str(), flon_s.c_str() );


        LMIC_setTxData2(1, mydata, strlen((char*)mydata), 0);

        sqn++;
        
        //LMIC_setTxData2(1, mydata, sizeof(mydata)-1, 0);
        Serial.println(F("Packet queued"));
        M5.Lcd.print("Packet queued ");
    }
    // Next TX is scheduled after TX_COMPLETE event.
}

void onEvent (ev_t ev) {
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
            /* do not print anything -- it wrecks timing */
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

void setup() {

    M5.begin(); 
    M5.Power.begin();
    M5.Lcd.print("Test LoRaWAN LMIC ");

    flat = DEFAULT_LAT;
    flon = DEFAULT_LON;
    

    while (! Serial);
       Serial.begin(115200);
       
    while (millis() < 5000) {
    Serial.print("millis() = "); Serial.println(millis());
    delay(500);
    }
    Serial.println(F("Starting"));
    M5.Lcd.print("Starting ");


    ss.begin(GPSBaud);
    

    #ifdef VCC_ENABLE
    // For Pinoccio Scout boards
    pinMode(VCC_ENABLE, OUTPUT);
    digitalWrite(VCC_ENABLE, HIGH);
    delay(1000);
    #endif

    // LMIC init
    os_init();
    // Reset the MAC state. Session and pending data transfers will be discarded.
    LMIC_reset();

    // Start job (sending automatically starts OTAA too)
    do_send(&sendjob);
}

void loop() {

    String flat_s;
    String flon_s;
    
    os_runloop_once();

    flat = gps.location.lat();
    flon = gps.location.lng();

    flat_s = String(flat,7);
    flon_s = String(flon,7);
  
    smartDelay(0);
}

static void smartDelay(unsigned long ms)
{
  unsigned long start = millis();
  do 
  {
    while (ss.available())
      gps.encode(ss.read());
  } while (millis() - start < ms);
}
