#!/usr/bin/env python3
import paho.mqtt.client as mqtt
import ssl

import time
import grovepi
import requests

# Global variable initialization
message_recu = 0
res = None 

# On-call function from the library paho.mqtt.client, used when an MQTT message is received  
def on_message(client, userdata, message) :
  global message_recu
  global res
  message_recu = message_recu + 1 # This variable is used to end waiting for a message
  res = message.payload.decode() # The message received is stored in a variable

# Function for checking presence based on received zone  
def verification_presence(resu) :
  if resu == "zone 1" :
    return True
    
  elif resu == "zone 2" :
    return True
    
  elif resu == "zone 3" :
    return True
    
  elif resu == "zone 4" :
    return True
    
  elif resu == "zone 5" :
    return True
    
  elif resu == "zone 6" :
    return True
    
  elif resu == "zone 7" :
    return True
    
  else :
    return False

# Function to send values to the database
def send_to_bdd(values):
  url = "http://sae51.rt-blagnac.fr/api/api.php"
  
  data = {
  "token": "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X",
  "values": ','.join([str(v) for v in values])
  }
  
  response = requests.post(url, data=data)
  
  if response.status_code == 201:
    pass
  else:
    print(f"Erreur {response.status_code}: {response.text}")


# MQTT broker configuration
broker = "192.168.45.155"
port = 1883
topic = "SDB/localisation"
username = "admin"
password = "passpartout"

# MQTT client "py" configuration
clientpy = mqtt.Client(client_id="Codepy")
clientpy.on_message = on_message
clientpy.username_pw_set(username, password)
clientpy.tls_set(tls_version=ssl.PROTOCOL_TLS, cert_reqs=ssl.CERT_NONE)

# MQTT client "py" connexion to the broker
clientpy.connect(broker, port, 60)
clientpy.subscribe(topic)
clientpy.loop_start()

# The script waits for at least one message is received
while message_recu < 1:
  pass
clientpy.loop_stop()

resultat = verification_presence(res)
clientpy.disconnect()


if resultat == True:
    # MQTT client "ls" configuration
    clientls = mqtt.Client(client_id="lightsensor")

    clientls.username_pw_set(username, password)
    clientls.tls_set(tls_version=ssl.PROTOCOL_TLS, cert_reqs=ssl.CERT_NONE)

    clientls.connect(broker, port, 60)

    # Light sensor configuration
    light_sensor = 0

    topic1 = "MIB/SDB/capteurlumiere/action" 

    # Thresholds determine the state of the light
    seuil_min = 200
    seuil_max = 500

    light = None
    sensor_value = 0

    grovepi.pinMode(light_sensor,"INPUT")

    try:
        # A loop to record the sensor value
        for compteur in range (3) :
            sensor_value = sensor_value + grovepi.analogRead(light_sensor)
            time.sleep(5)
            
        sensor_value = sensor_value / 3

        # According to the sensor value the state of the light is determined
        if sensor_value < seuil_min :
            light = "ALLUMER-LUMIERE" # Turn on LED once luminosity exceeds sensor_light value
            criticite = 2
   
        elif (sensor_value > seuil_max) :
            light = "ETEINDRE-LUMIERE" # Turn off LED once luminosity exceeds sensor_light value
            criticite = 2
      
        elif (seuil_min <= sensor_value <= seuil_max) :
            light = "INCHANGER"
            criticite = 1
         
        # Sending values to the topic1 and to the database          
        clientls.publish(topic1,light)
        values = [5, sensor_value, light, criticite]
        send_to_bdd(values)
    except IOError:
        print ("Error")

    clientls.disconnect()