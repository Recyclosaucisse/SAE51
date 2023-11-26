#!/usr/bin/env python
import time
from time import strftime, gmtime
import grovepi
import paho.mqtt.client as mqtt
import ssl

#Define the MQTT broker and the port
broker_address = "192.168.45.155"
port = 1883

#MQTT client creation
client = mqtt.Client("lightsensor")
username = "admin"
password = "passpartout"
client.username_pw_set(username, password)

#Set TLS Configuration
client.tls_set(tls_version=ssl.PROTOCOL_TLS, cert_reqs=ssl.CERT_NONE)

#Connection to the MQTT broker
client.connect(broker_address, port)

#Connect the Grove Light Sensor to analog port A0
light_sensor = 0

#Topic
topic1 = "MIB/salon/capteurlumiere/action"
topic2 = "MIB/salon/capteurlumiere/bdd"

# Connect the LED to digital port D4
led = 4

# Turn on LED once luminosity exceeds sensor_light value
seuil_min = 200

# Turn off LED once luminosity exceeds sensor_light value
seuil_max = 500

# to record if the light is ON or OFF
light = "none"

grovepi.pinMode(light_sensor,"INPUT")
grovepi.pinMode(led,"OUTPUT")

while True:
	try:
    	# Get sensor value
    	sensor_value = grovepi.analogRead(light_sensor)

    	if sensor_value < seuil_min :
        	# Send HIGH to switch on LED
        	grovepi.digitalWrite(led,1)
        	light = "allumer"
       	 
    	elif (sensor_value > seuil_max) :
        	# Send LOW to switch off LED
        	grovepi.digitalWrite(led,0)
        	light = "eteindre"
   	 
    	elif (seuil_min <= sensor_value <= seuil_max) :
        	light = "ne pas toucher"

    	temps = strftime('%H:%M:%S', gmtime())
    	sensor_value = str(sensor_value)
    	bdd = (temps, sensor_value, light)
    	bdd = str(bdd)
    	print (bdd)
    	client.publish(topic1,light)
    	client.publish(topic2,bdd)
    	print("sensor_value = ",(sensor_value),"; light ", (light))
    	time.sleep(1.5)
	except IOError:
    	print ("Error")

client.disconnect()
